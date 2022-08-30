<?php declare(strict_types=1);

namespace RatMD\BlogHub\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Illuminate\Support\Facades\DB;
use RainLab\Blog\Models\Post;
use RatMD\BlogHub\Models\Comment;

class CommentsList extends ReportWidgetBase
{

    /**
     * Initialize the widget, called by the constructor and free from its parameters.
     * 
     * @return void
     */
    public function init()
    {
    }

    /**
     * Initialize the properties of this widget.
     * 
     * @return void
     */
    public function defineProperties()
    {
        return [

        ];
    }

    /**
     * Renders the widget's primary contents.
     * 
     * @return string HTML markup supplied by this widget.
     */
    public function render()
    {
        $comments = Comment::where('status', 'pending')->orderBy('created_at', 'DESC')->limit(6)->get();

        if ($comments->count() === 0) {
            $comment = null;
        } else {
            $comment = $comments->shift();
        }

        return $this->makePartial('widget', [
            'status' => 'pending',
            'comment' => $comment,
            'list' => $comments,
            'counts' => [
                'pending' => Comment::where('status', 'pending')->count(),
                'approved' => Comment::where('status', 'approved')->count(),
                'rejected' => Comment::where('status', 'rejected')->count(),
                'spam' => Comment::where('status', 'spam')->count(),
            ]
        ]);
    }

    /**
     * Adds widget specific asset files. Use $this->addJs() and $this->addCss()
     * to register new assets to include on the page.
     * 
     * @return void
     */
    protected function loadAssets()
    {
        $this->addCss('/plugins/ratmd/bloghub/assets/css/widget-commentslist.css');
    }

    public function onLoadComment()
    {

    }

    public function onChangeStatus()
    {
        return [
            'status' => input('status'),
            'comment_id' => input('comment')
        ];
    }

}
