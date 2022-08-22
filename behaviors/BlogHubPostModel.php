<?php declare(strict_types=1);

namespace RatMD\BlogHub\Behaviors;

use October\Rain\Extension\ExtensionBase;
use RainLab\Blog\Models\Post;

class BlogHubPostModel extends ExtensionBase
{

    /**
     * Parent Post Model
     *
     * @var Post
     */
    protected Post $model;

    /**
     * Constructor
     *
     * @param Post $model
     */
    public function __construct(Post $model)
    {
        $this->model = $model;
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

}
