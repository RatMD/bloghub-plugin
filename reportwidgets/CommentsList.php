<?php declare(strict_types=1);

namespace RatMD\BlogHub\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Illuminate\Support\Facades\DB;
use RainLab\Blog\Models\Post;

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
        $posts = Post::orderBy('ratmd_bloghub_views', 'DESC')
            ->limit(5)
            ->get()
            ->all();


        $pastDay = date('Y-m-d', time()-7*24*60*60) . ' 00:00:00';
        $counts = Post::where('published_at', '>=', $pastDay)->get();

        /*
            options:
                last 7 days
                last 7 weeks
                last 7 months

         */

        $pastTime = strtotime($pastDay);
        $result = [];
        for ($i = 0; $i < 7; $i++) {
            $timestamp = $pastTime + ($i * 24 * 60 * 60);

            $start = date('Y-m-d', $timestamp) . ' 00:00:00';
            $end = date('Y-m-d', $timestamp + (24 * 60 * 60)) . ' 00:00:00';
            $result[date('d. M.', $timestamp)] = $counts->whereBetween('published_at', [$start, $end])->count();
        }
        
        return $this->makePartial('widget', [
            'posts' => $posts,
            'stats' => $result
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
    }

}
