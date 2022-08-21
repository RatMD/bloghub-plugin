<?php declare(strict_types=1);

namespace RatMD\BlogHub;

use Backend;
use Event;
use Exception;
use Backend\Controllers\Users as BackendUsers;
use Backend\Facades\BackendAuth;
use Backend\Models\User as BackendUser;
use Backend\Widgets\Form;
use Cms\Classes\Controller;
use Cms\Classes\Theme;
use Illuminate\Contracts\Database\Query\Builder;
use RainLab\Blog\Controllers\Posts;
use RainLab\Blog\Models\Post;
use RatMD\BlogHub\Models\Meta;
use RatMD\BlogHub\Models\Settings;
use RatMD\BlogHub\Models\Visitor;
use Symfony\Component\Yaml\Yaml;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    
    /**
     * Required Extensions
     *
     * @var array
     */
    public $require = ['RainLab.Blog'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'ratmd.bloghub::lang.plugin.name',
            'description' => 'ratmd.bloghub::lang.plugin.description',
            'author'      => 'RatMD <info@rat.md>',
            'icon'        => 'icon-tags',
            'homepage'    => 'https://github.com/RatMD/bloghub-plugin'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

        // Extend allowed sorting options
        Post::$allowedSortingOptions['bloghub_views asc'] = 'ratmd.bloghub::lang.sorting.bloghub_views_asc';
        Post::$allowedSortingOptions['bloghub_views desc'] = 'ratmd.bloghub::lang.sorting.bloghub_views_desc';
        Post::$allowedSortingOptions['bloghub_unique_views asc'] = 'ratmd.bloghub::lang.sorting.bloghub_unique_views_asc';
        Post::$allowedSortingOptions['bloghub_unique_views desc'] = 'ratmd.bloghub::lang.sorting.bloghub_unique_views_desc';
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('backend.menu.extendItems', function($manager) {
            $manager->addSideMenuItems('RainLab.Blog', 'blog', [
                'bloghub_tags' => [
                    'label' => 'ratmd.bloghub::lang.backend.tags.label',
                    'icon'  => 'icon-tags',
                    'code'  => 'tags',
                    'owner' => 'RainLab.Blog',
                    'url'   => Backend::url('ratmd/bloghub/tags')
                ]
            ]);
        });

        // Collect (Unique) Views
        Event::listen('cms.page.end', function (Controller $ctrl) {
            $post = $ctrl->getPageObject()->vars['post'] ?? null;
            if ($post && $post instanceof Post && BackendAuth::getUser() === null) {
                $visitor = Visitor::currentUser();
                if (!$visitor->hasSeen($post)) {
                    $post->bloghub_unique_views = is_numeric($post->bloghub_unique_views)? $post->bloghub_unique_views+1: 1;
                    $visitor->markAsSeen($post);
                }

                $post->bloghub_views = is_numeric($post->bloghub_views)? $post->bloghub_views+1: 1;
                $post->save();
            }
        });

        // Extend Post Model
        Post::extend(fn (Post $model) => $this->extendPostModel($model));

        // Extend Posts Controller
        Posts::extendFormFields(fn ($form, $model, $context) => $this->extendPostsController($form, $model, $context));

        // Extend Backend User Model
        BackendUser::extend(fn (BackendUser $model) => $this->extendBackendUserModel($model));

        // Extend Backend Users Controller
        BackendUsers::extendFormFields(fn ($form, $model, $context) => $this->extendBackendUsersController($form, $model, $context));
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'RatMD\BlogHub\Components\Authors'  => 'bloghubAuthorArchive',
            'RatMD\BlogHub\Components\Dates'    => 'bloghubDateArchive',
            'RatMD\BlogHub\Components\Tag'      => 'bloghubTagArchive',
            'RatMD\BlogHub\Components\Tags'     => 'bloghubTags',
        ];
    }

    /**
     * Registers any backend permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // We're using RainLab.Blog's provided permissions
    }

    /**
     * Registers backend navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [];
    }

    /**
     * Registers settings navigation items for this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'bloghub' => [
                'label'         => 'ratmd.bloghub::lang.backend.settings.label',
                'description'   => 'ratmd.bloghub::lang.backend.settings.description',
                'category'      => 'rainlab.blog::lang.blog.menu_label',
                'icon'          => 'icon-list-ul',
                'class'         => 'RatMD\BlogHub\Models\Settings',
                'order'         => 500,
                'keywords'      => 'blog post meta data',
                'permissions'   => ['rainlab.blog.manage_settings']
            ]
        ];
    }
    
    /**
     * Extend the Post Model
     *
     * @param Post $model
     * @return void
     */
    protected function extendPostModel(Post $model)
    {

        // Add Tag Relationship
        $model->belongsToMany['bloghub_tags'] = [
            'RatMD\BlogHub\Models\Tag',
            'table' => 'ratmd_bloghub_tags_posts',
            'order' => 'slug'
        ];

        // Add Custom Meta Relationship
        $model->morphMany['bloghub_meta'] = [
            'RatMD\BlogHub\Models\Meta',
            'table' => 'ratmd_bloghub_meta',
            'name' => 'metable',
        ];

        // Add Temporary Form JSONable
        $model->addJsonable('bloghub_meta_temp');

        // Handle Backend Form Submits
        $model->bindEvent('model.beforeSave', function () use ($model) {
            $metaset = $model->bloghub_meta_temp;
            if (empty($metaset)) {
                return;
            }
            unset($model->attributes['bloghub_meta_temp']);

            // Find Meta or Create a new one
            $existing = $model->bloghub_meta;

            foreach ($metaset AS $name => &$value) {
                $meta = $existing->where('name', '=', $name);
                if ($meta->count() === 1) {
                    $meta = $meta->first();
                } else {
                    $meta = new Meta(['name' => $name]);
                }

                $meta->value = $value;
                $value = $meta;
            }

            // Store Metaset
            if ($model->exists) {
                $model->bloghub_meta()->saveMany($metaset);
            } else {
                $model->bloghub_meta = $metaset;
            }
        });

        // Bind URLs (@todo find a better solution)
        $model->bindEvent('model.afterFetch', function () use ($model) {
            $tags = $model->bloghub_tags;
            if ($tags->count() === 0) {
                return;
            }

            /** @var Controller|null */
            $ctrl = Controller::getController();
            if ($ctrl instanceof Controller && !empty($ctrl->getLayout())) {
                $viewBag = $ctrl->getLayout()->getViewBag()->getProperties();
                
                // Set Tag URL
                if (isset($viewBag['bloghubTagPage'])) {
                    $tags->each(fn ($tag) => $tag->setUrl($viewBag['bloghubTagPage'], $ctrl));
                }
            }
        });

        // Dynamic Method - Create a [name] => [value] meta data map
        $model->addDynamicMethod('bloghub_meta_data', function () use ($model) {
            return $model->bloghub_meta->mapWithKeys(function ($item, $key) {
                return [$item['name'] => $item['value']];
            })->all();
        });

        // Dynamic Method - Receive Similar Posts from current Model
        $model->addDynamicMethod(
            'bloghub_similar_posts', 
            fn ($limit = 3, $exclude = null) => $this->getSimilarPosts($model, $limit, $exclude)
        );

        // Dynamic Method - Receive Random Posts from current Model
        $model->addDynamicMethod(
            'bloghub_random_posts', 
            fn ($limit = 3, $exclude = null) => $this->getRandomPosts($model, $limit, $exclude)
        );

        // Dynamic Method - Get Next Post in the same category
        $model->addDynamicMethod(
            'bloghub_next_post_in_category', 
            fn () => $this->getNextPostInCategory($model)
        );

        // Dynamic Method - Get Previous Post in the same category
        $model->addDynamicMethod(
            'bloghub_prev_post_in_category', 
            fn () => $this->getPrevPostInCategory($model)
        );

        // Dynamic Method - Get Next Post
        $model->addDynamicMethod(
            'bloghub_next_post', 
            fn() => $this->getNextPost($model)
        );

        // Dynamic Method - Get Previous Post
        $model->addDynamicMethod(
            'bloghub_prev_post', 
            fn() => $this->getPrevPost($model)
        );
    }

    /**
     * Extends Posts Controller
     *
     * @param Form $form
     * @param mixed $model
     * @param mixed $context
     * @return void
     */
    protected function extendPostsController(Form $form, $model, $context)
    {
        if (!$model instanceof Post) {
            return;
        }

        // Build Meta Map
        $meta = $model->bloghub_meta->mapWithKeys(fn ($item, $key) => [$item['name'] => $item['value']])->all();

        // Add Tags Field
        $form->addSecondaryTabFields([
            'bloghub_tags' => [
                'label'     => 'ratmd.bloghub::lang.backend.tags.label',
                'mode'      => 'relation',
                'tab'       => 'rainlab.blog::lang.post.tab_categories',
                'type'      => 'taglist',
                'nameFrom'  => 'slug'
            ]
        ]);

        // Custom Meta Data
        $config = [];
        $settings = Settings::get('meta_data', []);
        if (is_array($settings)) {
            foreach (Settings::get('meta_data', []) AS $item) {
                try {
                    $temp = Yaml::parse($item['config']);
                } catch (Exception $e) {
                    $temp = null;
                }
                if (empty($temp)) {
                    continue;
                }

                $config[$item['name']] = $temp;
                $config[$item['name']]['type'] = $item['type'];

                // Add Label if missing
                if (empty($config[$item['name']]['label'])) {
                    $config[$item['name']]['label'] = $item['name'];
                }
            }
        }
        $config = array_merge($config, Theme::getActiveTheme()->getConfig()['ratmd.bloghub']['post'] ?? []);

        // Add Custom Meta Fields
        if (!empty($config)) {
            foreach ($config AS $key => $value) {
                if (empty($value['tab'])) {
                    $value['tab'] = 'ratmd.bloghub::lang.backend.meta.tab';
                }
                $form->addSecondaryTabFields([
                    "bloghub_meta_temp[$key]" => array_merge($value, [
                        'value' => $meta[$key] ?? '',
                        'default' => $meta[$key] ?? ''
                    ])
                ]);
            }
        }
    }

    /**
     * Extend the BackendUser Model
     *
     * @param BackendUser $model
     * @return void
     */
    protected function extendBackendUserModel(BackendUser $model)
    {
        $model->addDynamicMethod('bloghub_display', function () use ($model) {
            if (!empty($model->display_name)) {
                return $model->display_name;
            }
            
            $name = '';
            if ($model->first_name) {
                $name = $model->first_name;
            }
            if ($model->last_name) {
                $name = ($model->last_name? ' ': '') . $model->first_name;
            }
            return empty($name)? ucfirst($model->login): $name;
        });
        $model->addDynamicMethod('bloghub_slug', fn() => $model->author_slug ?? $model->login);
    }

    /**
     * Extend the BackendUsers Model1
     *
     * @param Form $form
     * @param mixed $model
     * @param mixed $context
     * @return void
     */
    protected function extendBackendUsersController(Form $form, $model, $context)
    {
        if (!$model instanceof BackendUser) {
            return;
        }

        // Add Display Name
        $form->addTabFields([
            'display_name' => [
                'label'         => 'ratmd.bloghub::lang.backend_users.display_name.label',
                'description'   => 'ratmd.bloghub::lang.backend_users.display_name.description',
                'tab'           => 'backend::lang.user.account',
                'type'          => 'text',
                'span'          => 'left'
            ],
            'author_slug' => [
                'label'         => 'ratmd.bloghub::lang.backend_users.author_slug.label',
                'description'   => 'ratmd.bloghub::lang.backend_users.author_slug.description',
                'tab'           => 'backend::lang.user.account',
                'type'          => 'text',
                'span'          => 'right'
            ],
            'about_me' => [
                'label'         => 'ratmd.bloghub::lang.backend_users.about_me.label',
                'description'   => 'ratmd.bloghub::lang.backend_users.about_me.description',
                'tab'           => 'backend::lang.user.account',
                'type'          => 'textarea',
            ]
        ]);
    }

    /**
     * Bind Post Archive URLs
     *
     * @param mixed $posts
     * @return mixed
     */
    protected function bindUrls($posts)
    {
        /** @var Controller */
        $ctrl = Controller::getController();

        if ($ctrl->getLayout()->hasComponent('blogPosts')) {
            $component = $ctrl->getLayout()->getComponentProperties('blogPosts');
            $viewBag = $ctrl->getLayout()->getViewBag()->getProperties();

            // Set Post URL
            if ($posts instanceof Post) {
                $posts->setUrl($component['postPage'], $ctrl);
            } else if (is_array($posts)) {
                array_walk($posts, fn($post) => $post->setUrl($component['postPage'], $ctrl));
            }

            // Set Author URL
            if (isset($viewBag['bloghubAuthorPage'])) {
                
            }
            
            // Set Date URL
            if (isset($viewBag['bloghubDatePage'])) {
                
            }
            
            // Set Tag URL
            if (isset($viewBag['bloghubTagPage'])) {
                if ($posts instanceof Post) {
                    $posts->bloghub_tags->each(fn ($tag) => $tag->setUrl($viewBag['bloghubTagPage'], $ctrl));
                } else if (is_array($posts)) {
                    array_walk(
                        $posts, 
                        fn($post) => $post->bloghub_tags->each(fn ($tag) => $tag->setUrl($viewBag['bloghubTagPage'], $ctrl))
                    );
                }
            }
        }

        return $posts;
    }

    /**
     * Get Similar Posts (based on Category and/or Tags)
     *
     * @param Post $post
     * @param int $limit
     * @param mixed $excludes Excluded post id (string or int), multiple as array.
     * @return array
     */
    protected function getSimilarPosts(Post $model, int $limit = 3, $exclude = null)
    {
        $tags = $model->bloghub_tags->map(fn ($item) => $item->id)->all();
        $categories = $model->categories->map(fn ($item) => $item->id)->all();

        // Exclude
        $excludes = [];
        if (!empty($exclude)) {
            $excludes = is_array($exclude)? $exclude: [$exclude];
            $excludes = array_map('intval', $excludes);
        }
        $excludes[] = $model->id;

        // Query
        $query = Post::with(['categories', 'featured_images', 'bloghub_tags'])
            ->whereHas('categories', function(Builder $query) use ($categories) {
                return $query->whereIn('rainlab_blog_categories.id', $categories);
            })
            ->whereHas('bloghub_tags', function(Builder $query) use ($tags) {
                return $query->whereIn('ratmd_bloghub_tags.id', $tags);
            })
            ->limit($limit);
        
        // Return Result
        $result = $query->get()->filter(fn($item) => !in_array($item['id'], $excludes))->all();
        return $this->bindUrls($result);
    }

    /**
     * Get Random Posts
     *
     * @param Post $post
     * @param int $limit
     * @param mixed $excludes Excluded post id (string or int), multiple as array.
     * @return array
     */
    protected function getRandomPosts(Post $model, int $limit = 3, $exclude = null)
    {

        // Exclude
        $excludes = [];
        if (!empty($exclude)) {
            $excludes = is_array($exclude)? $exclude: [$exclude];
            $excludes = array_map('intval', $excludes);
        }
        $excludes[] = $model->id;

        // Query
        $query = Post::with(['categories', 'featured_images', 'bloghub_tags'])->limit($limit);

        // Return Result
        $result = $query->get()->filter(fn($item) => !in_array($item['id'], $excludes))->all();
        return $this->bindUrls($result);
    }

    /**
     * Get Next Post in the same Category
     *
     * @param Post $model
     * @return Post|null
     */
    protected function getNextPostInCategory(Post $model)
    {
        $categories = $model->categories->map(fn($item) => $item->id)->all();
        $query = $model->applySibling()
            ->with('categories')
            ->whereHas('categories', function(Builder $query) use ($categories) {
                return $query->whereIn('rainlab_blog_categories.id', $categories);
            });
            
        return $this->bindUrls($query->first());
    }

    /**
     * Get Previous Post in the same Category
     *
     * @param Post $model
     * @return Post|null
     */
    protected function getPrevPostInCategory(Post $model)
    {
        $categories = $model->categories->map(fn ($item) => $item->id)->all();
        $query = $model->applySibling(-1)
            ->with('categories')
            ->whereHas('categories', function(Builder $query) use ($categories) {
                return $query->whereIn('rainlab_blog_categories.id', $categories);
            });
        
        return $this->bindUrls($query->first());
    }

    /**
     * Get Next Post
     *
     * @param Post $model
     * @return Post|null
     */
    protected function getNextPost(Post $model)
    {
        return $model->nextPost();
    }

    /**
     * Get Previous Post
     *
     * @param Post $model
     * @return Post|null
     */
    protected function getPrevPost(Post $model)
    {
        return $model->previousPost();
    }
    
}
