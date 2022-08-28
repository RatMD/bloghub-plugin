<?php declare(strict_types=1);

namespace RatMD\BlogHub\Behaviors;

use Cms\Classes\Controller;
use October\Rain\Extension\ExtensionBase;
use RainLab\Blog\Models\Post;
use RatMD\BlogHub\Classes\BlogHubPost;
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
            'RatMD\BlogHub\Models\Meta',
            'table' => 'ratmd_bloghub_meta',
            'name' => 'metable',
        ];

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
    }

    /**
     * Get main BlogHub Space
     *
     * @return array
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

    /**
     * Calculate ReadTime attribute.
     *
     * @return mixed
     */
    public function getReadTimeAttribute()
    {
        $content = strip_tags($this->model->content_html);
        $count = str_word_count($content);

        $amount = $count / 200;
        $minutes = intval($amount);
        $seconds = intval(($minutes > 0? $amount - $minutes: $amount) * 0.60 * 100); 

        if ($minutes === 0) {
            return trans('ratmd.bloghub::lang.model.post.reading_time_sec', [
                'sec' => $seconds
            ]);
        } else {
            return trans('ratmd.bloghub::lang.model.post.reading_time', [
                'min' => $minutes,
                'sec' => $seconds
            ]);
        }
    }

    /**
     * Get "published x x ago" string
     *
     * @return void
     */
    public function getPublishedAgoAttribute()
    {
        $seconds = (time() - strtotime($this->model->attributes['published_at']));

        if ($seconds >= 31536000) {
            $amount = intval($seconds / 31536000);
            $format = 'years';
        } elseif ($seconds >= 2419200) {
            $amount = intval($seconds / 2419200);
            $format = 'months';
        } elseif ($seconds >= 86400) {
            $amount = intval($seconds / 86400);
            $format = 'days';
        } elseif ($seconds >= 3600) {
            $amount = intval($seconds / 3600);
            $format = 'hours';
        } elseif ($seconds >= 60) {
            $amount = intval($seconds / 60);
            $format = 'minutes';
        } else {
            return trans('ratmd.bloghub::lang.model.post.published_seconds_ago');
        }

        return trans('ratmd.bloghub::lang.model.post.published_ago', [
            'amount' => $amount,
            'format' => trans('ratmd.bloghub::lang.model.post.published_format_' . $format)
        ]);
    }

}
