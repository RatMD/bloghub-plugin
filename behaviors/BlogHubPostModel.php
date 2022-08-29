<?php declare(strict_types=1);

namespace RatMD\BlogHub\Behaviors;

use Cms\Classes\Controller;
use October\Rain\Extension\ExtensionBase;
use RainLab\Blog\Models\Post;
<<<<<<< HEAD
=======
use RatMD\BlogHub\Classes\BlogHubPost;
>>>>>>> bd5ef37 ([DEV])
use RatMD\BlogHub\Models\Meta;

class BlogHubPostModel extends ExtensionBase
{

    /**
     * Parent Post Model
     *
     * @var Post
     */
    protected Post $model;

    /**
<<<<<<< HEAD
=======
     * BlogHub Post Model DataSet
     *
     * @var ?BlogHubPost
     */
    protected ?BlogHubPost $bloghubSet;

    /**
>>>>>>> bd5ef37 ([DEV])
     * Constructor
     *
     * @param Post $model
     */
    public function __construct(Post $model)
    {
        $this->model = $model;

<<<<<<< HEAD
        // Add Tag Relationship
        $this->model->belongsToMany['ratmd_bloghub_tags'] = [
            'RatMD\BlogHub\Models\Tag',
            'table' => 'ratmd_bloghub_tags_posts',
            'order' => 'slug'
        ];

        // Add Custom Meta Relationship
        $this->model->morphMany['ratmd_bloghub_meta'] = [
=======
        // Add Blog Comments
        $model->hasMany['ratmd_bloghub_comments'] = [
            'RatMD\BlogHub\Models\Comment',
            'table' => 'ratmd_bloghub_comments',
            'order' => 'slug'
        ];

        $model->belongsToMany['ratmd_bloghub_comments_count'] = [
            'RatMD\BlogHub\Models\Comment',
            'table' => 'ratmd_bloghub_comments',
            'order' => 'slug',
            'count' => true
        ];

        // Add Blog Meta
        $model->morphMany['ratmd_bloghub_meta'] = [
>>>>>>> bd5ef37 ([DEV])
            'RatMD\BlogHub\Models\Meta',
            'table' => 'ratmd_bloghub_meta',
            'name' => 'metable',
        ];

<<<<<<< HEAD
        // Add Temporary Form JSONable
        $this->model->addJsonable('ratmd_bloghub_meta_temp');
        
        // Handle Backend Form Submits
        $model->bindEvent('model.beforeSave', fn() => $this->beforeSave());

        // Bind URLs (@todo find a better solution)
        $model->bindEvent('model.afterFetch', fn() => $this->afterFetch());
=======
        // Add Blog Tags
        $model->belongsToMany['ratmd_bloghub_tags'] = [
            'RatMD\BlogHub\Models\Tag',
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
>>>>>>> bd5ef37 ([DEV])
    }

    /**
     * Before Save Hook
     *
     * @return void
     */
    protected function beforeSave()
    {
        $metaset = $this->model->ratmd_bloghub_meta_temp;
        if (empty($metaset)) {
            return;
        }
        unset($this->model->attributes['ratmd_bloghub_meta_temp']);

        // Find Meta or Create a new one
        $existing = $this->model->ratmd_bloghub_meta;

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
        if ($this->model->exists) {
            $this->model->ratmd_bloghub_meta()->saveMany($metaset);
        } else {
            $this->model->ratmd_bloghub_meta = $metaset;
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
