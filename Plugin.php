<?php declare(strict_types=1);

namespace RatMD\BlogHub;

use Backend;
use Event;
use Backend\Controllers\Files;
use Backend\FormWidgets\FileUpload;
use Backend\Widgets\Form;
use Cms\Classes\Theme;
use Illuminate\Contracts\Database\Query\Builder;
use October\Rain\Database\Collection;
use October\Rain\Database\QueryBuilder;
use RainLab\Blog\Controllers\Posts;
use RainLab\Blog\Models\Post;
use RatMD\BlogHub\Models\Meta;
use System\Classes\PluginBase;
use System\Models\File;

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
                'bhub_tags' => [
                    'label' => 'ratmd.bloghub::lang.backend.tags.label',
                    'icon'  => 'icon-tags',
                    'code'  => 'tags',
                    'owner' => 'RainLab.Blog',
                    'url'   => Backend::url('ratmd/bloghub/tags')
                ]
            ]);
        });

        // Extend Post Model
        Post::extend(function (Post $model) {

            // Inject Tags
            $model->belongsToMany['bhub_tags'] = [
                'RatMD\BlogHub\Models\Tag',
                'table' => 'ratmd_bloghub_tags_posts',
                'order' => 'slug'
            ];

            // Inject Custom Meta
            $model->morphMany['bhub_meta'] = [
                'RatMD\BlogHub\Models\Meta',
                'table' => 'ratmd_bloghub_meta',
                'name' => 'metable',
            ];

            // Provide key => value Map for Custom Meta
            $model->addDynamicMethod('bhub_meta_data', function () use ($model) {
                return $model->bhub_meta->mapWithKeys(function ($item, $key) {
                    return [$item['name'] => $item['value']];
                })->all();
            });

            // Provide additional Post Model methods
            $model->addDynamicMethod('bhub_similar_posts', fn($exclude = null, $limit = 3) 
                => $this->getSimilarPosts($model, $exclude, $limit));
            $model->addDynamicMethod('bhub_random_posts', fn($exclude = null, $limit = 3) 
                => $this->getRandomPosts($model, $exclude, $limit));

            // Insert Custom Meta
            $model->bindEvent('model.beforeSave', function () use ($model) {
                $result = [];

                /** @var Collection */
                $existing = $model->bhub_meta;
                foreach ($model->getAttributes() AS $key => $value) {
                    if (strpos($key, 'bhub_meta__') !== 0) {
                        continue;
                    }
                    
                    $name = substr($key, 11);

                    // Get or Create Meta
                    $meta = $existing->where('name', '=', $name);
                    if ($meta->count() === 1) {
                        $meta = $meta->first();
                    } else {
                        $meta = new Meta([
                            'name' => substr($key, 11),
                            'value' => $value,
                        ]);
                    }

                    // Append
                    $meta->value = $value;
                    $result[] = $meta;
                    unset($model->attributes[$key]);
                }

                if ($model->exists) {
                    $model->bhub_meta()->saveMany($result);
                } else {
                    $model->bhub_meta = $result;
                }
            });
        });

        // Extend Posts Controller
        Posts::extendFormFields(function($form, $model, $context) {
            if (!$model instanceof Post) {
                return;
            }
            $meta = $model->bhub_meta->mapWithKeys(function ($item, $key) {
                return [$item['name'] => $item['value']];
            })->all();

            // Add Tags Field
            $form->addSecondaryTabFields([
                'bhub_tags' => [
                    'label'     => 'ratmd.bloghub::lang.backend.tags.label',
                    'mode'      => 'relation',
                    'tab'       => 'rainlab.blog::lang.post.tab_categories',
                    'type'      => 'taglist',
                    'nameFrom'  => 'slug'
                ]
            ]);

            // Add Custom Meta Fields
            $config = Theme::getActiveTheme()->getConfig()['ratmd.bloghub']['post'] ?? [];
            foreach ($config AS $key => $value) {
                $form->addSecondaryTabFields([
                    "bhub_meta__{$key}" => array_merge($value, [
                        'tab' => 'ratmd.bloghub::lang.backend.meta.tab',
                        'value' => $meta[$key] ?? '',
                        'default' => $meta[$key] ?? ''
                    ])
                ]);
            }
        });

        File::extend(function (File $model) {

            // Inject Custom Meta
            $model->morphMany['bhub_meta'] = [
                'RatMD\BlogHub\Models\Meta',
                'table' => 'ratmd_bloghub_meta',
                'name' => 'metable',
            ];

            // Provide key => value Map for Custom Meta
            $model->addDynamicMethod('bhub_meta_data', function () use ($model) {
                return $model->bhub_meta->mapWithKeys(function ($item, $key) {
                    return [$item['name'] => $item['value']];
                })->all();
            });

        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate
    }

    /**
     * Registers any backend permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate
    }

    /**
     * Registers backend navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate
    }

    /**
     * Get Similar Posts (based on Category and/or Tags)
     *
     * @param Post $post
     * @param mixed $excludes Excluded post id (string or int), multiple as array.
     * @param int $limit
     * @return array
     */
    protected function getSimilarPosts(Post $model, $excludes = null, int $limit = 3)
    {
        $tags = $model->bhub_tags->map(fn($item) => $item->id)->all();
        $categories = $model->categories->map(fn($item) => $item->id)->all();

        // Exclude
        $excludes = [];
        if (!empty($exclude)) {
            $excludes = is_array($exclude)? $exclude: [$exclude];
            $excludes = array_map('intval', $excludes);
        }
        $excludes[] = $model->id;

        // Query
        $query = Post::with(['categories', 'featured_images'])
            ->whereHas('categories', function(Builder $query) use ($categories) {
                return $query->whereIn('rainlab_blog_categories.id', $categories);
            })
            ->whereHas('bhub_tags', function(Builder $query) use ($tags) {
                return $query->whereIn('ratmd_bloghub_tags.id', $tags);
            })
            ->limit($limit);
        
        // Return Result
        $result = $query->get()->filter(fn($item) => !in_array($item['id'], $excludes))->all();
        return $result;
    }

    /**
     * Get Random Posts
     *
     * @param Post $post
     * @param mixed $excludes Excluded post id (string or int), multiple as array.
     * @param int $limit
     * @return array
     */
    protected function getRandomPosts(Post $model, $excludes = null, int $limit = 3)
    {

        // Exclude
        $excludes = [];
        if (!empty($exclude)) {
            $excludes = is_array($exclude)? $exclude: [$exclude];
            $excludes = array_map('intval', $excludes);
        }
        $excludes[] = $model->id;

        // Query
        $query = Post::with(['categories', 'featured_images'])->limit($limit);
        
        // Return Result
        $result = $query->get()->filter(fn($item) => !in_array($item['id'], $excludes))->all();
        return $result;
    }
    
}
