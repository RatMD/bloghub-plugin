<?php declare(strict_types=1);

namespace RatMD\BlogHub\Behaviors;

use Cms\Classes\Controller;
use October\Rain\Extension\ExtensionBase;
use RainLab\Blog\Models\Post;
use RatMD\BlogHub\Classes\BlogHubPost;
use RatMD\BlogHub\Models\Comment;
use RatMD\BlogHub\Models\Meta;
use RatMD\BlogHub\Models\Tag;

class BlogHubPostModel extends ExtensionBase
{

    /**
     * Parent Post Model
     *
     * @var Post
     */
    protected Post $model;

    /**
     * BlogHub Post Model DataSet
     *
     * @var ?BlogHubPost
     */
    protected ?BlogHubPost $bloghubSet;

    /**
     * Constructor
     *
     * @param Post $model
     */
    public function __construct(Post $model)
    {
        $this->model = $model;

        // Add Blog Comments
        $model->hasMany['ratmd_bloghub_comments'] = [
            Comment::class
        ];

        $model->hasMany['ratmd_bloghub_comments_count'] = [
            Comment::class,
            'count' => true
        ];

        // Add Blog Meta
        $model->morphMany['ratmd_bloghub_meta'] = [
            Meta::class,
            'table' => 'ratmd_bloghub_meta',
            'name' => 'metable',
        ];

        // Add Blog Tags
        $model->belongsToMany['ratmd_bloghub_tags'] = [
            Tag::class,
            'table' => 'ratmd_bloghub_tags_posts',
            'order' => 'slug'
        ];

        // Add Temporary Form JSONable
        $model->addJsonable('ratmd_bloghub_meta_temp');
        
        // Handle Backend Form Submits
        $model->bindEvent('model.beforeSave', fn() => $this->beforeSave());
        
        // Register Tags Scope
        $model->addDynamicMethod('scopeFilterTags', function ($query, $tags) {
            return $query->whereHas('ratmd_bloghub_tags', function($q) use ($tags) {
                $q->withoutGlobalScope(NestedTreeScope::class)->whereIn('id', $tags);
            });
        });
        
        // Register Deprecated Methods
        $model->bindEvent('model.afterFetch', fn() => $this->registerDeprecatedMethods($model));
    }

    /**
     * Register deprecated methods
     *
     * @param Post $model
     * @return void
     */
    protected function registerDeprecatedMethods(Post $model)
    {
        $bloghub = $this->getBloghubAttribute();

        // Dynamic Method - Create a [name] => [value] meta data map
        $model->addDynamicMethod(
            'ratmd_bloghub_meta_data', 
            fn () => $bloghub->getMeta()
        );

        // Dynamic Method - Receive Similar Posts from current Model
        $model->addDynamicMethod(
            'bloghub_similar_posts', 
            fn ($limit = 3, $exclude = null) => $bloghub->getRelated($limit, $exclude)
        );

        // Dynamic Method - Receive Random Posts from current Model
        $model->addDynamicMethod(
            'bloghub_random_posts', 
            fn ($limit = 3, $exclude = null) => $bloghub->getRandom($limit, $exclude)
        );

        // Dynamic Method - Get Next Post in the same category
        $model->addDynamicMethod(
            'bloghub_next_post_in_category', 
            fn () => $bloghub->getNext(1, true)
        );

        // Dynamic Method - Get Previous Post in the same category
        $model->addDynamicMethod(
            'bloghub_prev_post_in_category', 
            fn () => $bloghub->getPrevious(1, true)
        );

        // Dynamic Method - Get Next Post
        $model->addDynamicMethod(
            'bloghub_next_post', 
            fn() => $bloghub->getNext()
        );

        // Dynamic Method - Get Previous Post
        $model->addDynamicMethod(
            'bloghub_prev_post', 
            fn() => $bloghub->getPrevious()
        );
    }

    /**
     * Get main BlogHub Space
     *
     * @return BlogHubPost
     */
    public function getBloghubAttribute()
    {
        if (empty($this->bloghubSet)) {
            $this->bloghubSet = new BlogHubPost($this->model);
        }
        return $this->bloghubSet;
    }

    /**
     * Before Save Hook
     *
     * @return void
     */
    protected function beforeSave()
    {
        $metaset = $this->model->ratmd_bloghub_meta_temp;
        /* unset $this->model->attributes['ratmd_bloghub_meta_temp'] if metaset is Empty */
        if (empty($metaset)) {
            unset($this->model->attributes['ratmd_bloghub_meta_temp']);
            return;
        }
        unset($this->model->attributes['ratmd_bloghub_meta_temp']);

        // Find Meta or Create a new one
        $existing = $this->model->ratmd_bloghub_meta;

        foreach ($metaset AS $name => &$value) {
            $meta = $existing->where('name', '=', $name);
            if ($meta->count() === 1) {
                $meta = $meta->first();
                $meta->value = $value;
            } else {
                $meta = new Meta([
                    'name' => $name, 
                    'value' => $value
                ]);
                $meta->metable_type = get_class($this->model);
                $meta->save();
            }

            $value = $meta;
        }

        // Store Metaset
        if ($this->model->exists) {
            $this->model->ratmd_bloghub_meta()->saveMany($metaset);
        } else {
            $model = $this->model;
            $sessionKey = uniqid('session_key', true);

            $this->model->sessionKey = $sessionKey;
            array_walk($metaset, function($meta) use ($model, $sessionKey) {
                $model->ratmd_bloghub_meta()->add($meta, $sessionKey);
            });
        }
    }

    /**
     * After Fetch Hook
     *
     * @return void
     */
    protected function afterFetch()
    {
        $tags = $this->model->ratmd_bloghub_tags;
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
    }

}
