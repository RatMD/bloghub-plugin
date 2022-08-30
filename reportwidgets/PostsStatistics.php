<?php declare(strict_types=1);

namespace RatMD\BlogHub\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use DateInterval;
use DateTime;
use Lang;
use RainLab\Blog\Models\Post;
use System\Classes\UpdateManager;

class PostsStatistics extends ReportWidgetBase
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
            'defaultDateRange' => [
                'title'         => 'ratmd.bloghub::lang.components.post.date_range',
                'description'   => 'ratmd.bloghub::lang.components.post.date_range_comment',
                'type'          => 'dropdown',
                'default'       => '14 days',
            ],
        ];
    }

    /**
     * Get Default Date Range Options
     *
     * @return array
     */
    public function getDefaultdateRangeOptions()
    {
        return [
            '7 days'        => Lang::get('ratmd.bloghub::lang.components.post.7days'),
            '14 days'       => Lang::get('ratmd.bloghub::lang.components.post.14days'),
            '31 days'       => Lang::get('ratmd.bloghub::lang.components.post.31days'),
            '3 months'      => Lang::get('ratmd.bloghub::lang.components.post.3months'),
            '6 months'      => Lang::get('ratmd.bloghub::lang.components.post.6months'),
            '12 months'     => Lang::get('ratmd.bloghub::lang.components.post.12months')
        ];
    }

    /**
     * Adds widget specific asset files. Use $this->addJs() and $this->addCss()
     * to register new assets to include on the page.
     * 
     * @return void
     */
    protected function loadAssets()
    {
        if (version_compare(UpdateManager::instance()->getCurrentVersion(), '3.0.0', '<')) {
            $this->addCss('/plugins/ratmd/bloghub/assets/css/widget-octoberv2.css');
        }
    }

    /**
     * Get Published Post Statistics
     *
     * @return array
     */
    protected function getPublishedStatistics($range)
    {
        $interval = DateInterval::createFromDateString($range);
        $datetime = (new DateTime())
                ->setTime(0, 0, 0, 0)
                ->sub($interval);

        // Filter Posts
        $posts = Post::where('published', '1')
                    ->where('published_at', '>=', $datetime->format('Y-m-d') . ' 00:00:00')
                    ->get();
        
        // Build Graph Data
        $number = intval(explode(' ', $range)[0]);
        $steps = (strpos($range, 'days') === false ? $number * 31 : $number) / 7;
        $result = [];

        for ($i = 0; $i < 7; $i++) {
            $step = intval($i === 6 ? ceil($steps) : floor($steps));
            
            $timestamp = $datetime->getTimestamp() + ($i * $step * 24 * 60 * 60);
            $start = date('Y-m-d', $timestamp) . ' 00:00:00';
            $end = date('Y-m-d', $timestamp + ($step * 24 * 60 * 60)) . ' 00:00:00';

            $count = $posts->whereBetween('published_at', [$start, $end])->count();
            if ($steps === 1) {
                $result[date('d. M.', $timestamp)] = $count;
            } else {
                $key = date('d. M.', $timestamp) . ' - ' . date('d. M.', $timestamp + ($step * 24 * 60 * 60));
                $result[$key] = $count;
            }
        }
        return $result;
    }

    /**
     * Get General Post Statistics
     *
     * @param string The desired range
     * @return array
     */
    protected function getGeneralStatistics($range)
    {
        $interval = DateInterval::createFromDateString($range);
        $datetime = (new DateTime())
                ->setTime(0, 0, 0, 0)
                ->sub($interval);

        // Filter Posts
        $posts = Post::where('published', '1')
                    ->where('published_at', '>=', $datetime->format('Y-m-d') . ' 00:00:00')
                    ->get();
        
        // Build Graph Data
        $number = intval(explode(' ', $range)[0]);
        $steps = (strpos($range, 'days') === false ? $number * 31 : $number) / 7;
        $result = [
            'views' => [],
            'visitors' => []
        ];

        for ($i = 0; $i < 7; $i++) {
            $step = intval($i === 6 ? ceil($steps) : floor($steps));
            
            $timestamp = $datetime->getTimestamp() + ($i * $step * 24 * 60 * 60);
            $start = date('Y-m-d', $timestamp) . ' 00:00:00';
            $end = date('Y-m-d', $timestamp + ($step * 24 * 60 * 60)) . ' 00:00:00';

            $count = $posts->whereBetween('published_at', [$start, $end]);
            $result['views'][] = '[' . $timestamp*1000 . ', ' . $count->sum('ratmd_bloghub_views') . ']';
            $result['visitors'][] = '[' . $timestamp*1000 . ', ' . $count->sum('ratmd_bloghub_unique_views') . ']';
        }

        return $result;
    }

    /**
     * Renders the widget's primary contents.
     * 
     * @return string HTML markup supplied by this widget.
     */
    public function render()
    {
        $range = $this->property('defaultDateRange', '14 days');
        if (!array_key_exists($range, $this->getDefaultdateRangeOptions())) {
            $range = '14 days';
        }
        
        // Render Partial
        return $this->makePartial('widget', [
            'range' => $range,
            'publishedStatistics' => $this->makePartial('published-statistics', [
                'statistics' => $this->getPublishedStatistics($range),
            ]),
            'generalStatistics' => $this->makePartial('general-statistics', [
                'statistics' => $this->getGeneralStatistics($range),
            ])
        ]);
    }

    /**
     * AJAX Handler - Change Date Ramge
     *
     * @return array
     */
    public function onChangeRange()
    {
        $range = input('range');
        if (!array_key_exists($range, $this->getDefaultdateRangeOptions())) {
            $range = '14 days';
        }
        
        // Render Partial
        return [
            'range' => $range,
            '#publishedStatistics' => $this->makePartial('published-statistics', [
                'statistics' => $this->getPublishedStatistics($range),
            ]),
            '#generalStatistics' => $this->makePartial('general-statistics', [
                'statistics' => $this->getGeneralStatistics($range),
            ])
        ];
    }

}
