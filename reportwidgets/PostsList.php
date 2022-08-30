<?php declare(strict_types=1);

namespace RatMD\BlogHub\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use Cms\Classes\Controller;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use Lang;
use RainLab\Blog\Models\Post;
use System\Classes\UpdateManager;

class PostsList extends ReportWidgetBase
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
            'postPage' => [
                'title'         => 'rainlab.blog::lang.settings.posts_post',
                'description'   => 'rainlab.blog::lang.settings.posts_post_description',
                'type'          => 'dropdown',
                'default'       => 'blog/post',
            ],
            'defaultOrder' => [
                'title'         => 'ratmd.bloghub::lang.components.post.default_order',
                'description'   => 'ratmd.bloghub::lang.components.post.default_order_comment',
                'type'          => 'dropdown',
                'default'       => 'blog/post',
            ]
        ];
    }

    /**
     * Get Post Page Dropdown Options
     *
     * @return mixed
     */
    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * Get Post Page Dropdown Options
     *
     * @return mixed
     */
    public function getDefaultOrderOptions()
    {
        return [
            'by_published' => Lang::get('ratmd.bloghub::lang.components.post.by_published'),
            'by_views' => Lang::get('ratmd.bloghub::lang.components.post.by_views'),
            'by_visitors' => Lang::get('ratmd.bloghub::lang.components.post.by_visitors')
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
     * Get Posts
     *
     * @param string $order
     * @return mixed
     */
    protected function getPosts($order)
    {
        $query = Post::limit(10);
        if ($order === 'by_published') {
            $posts = $query
                ->where('published', '1')
                ->orderBy('published_at', 'DESC')
                ->get();
        } else if ($order === 'by_views') {
            $posts = $query
                ->where('published', '1')
                ->orderBy('ratmd_bloghub_views', 'DESC')
                ->orderBy('published_at', 'DESC')
                ->get();
        } else if ($order === 'by_visitors') {
            $posts = $query
                ->where('published', '1')
                ->orderBy('ratmd_bloghub_unique_views', 'DESC')
                ->orderBy('published_at', 'DESC')
                ->get();
        } else {
            $posts = $query->get();
        }

        if (!empty($postPage = $this->property('postPage'))) {
            $posts->each(fn ($item) => $item->setUrl($postPage, new Controller(Theme::getActiveTheme())));
        }
        return $posts;
    }

    /**
     * Renders the widget's primary contents.
     * 
     * @return string HTML markup supplied by this widget.
     */
    public function render()
    {
        $order = $this->property('defaultOrder', 'by_published');
        if (!array_key_exists($order, $this->getDefaultOrderOptions())) {
            $order = 'by_published';
        }
        
        $posts = $this->getPosts($order);
        return $this->makePartial('widget', [
            'order' => $order,
            'posts' => $posts,
            'postsPartial' => $this->makePartial('posts', [
                'posts' => $posts
            ]),
            'counts' => [
                'total' => Post::count(),
                'published' => Post::where('published', 1)->where('published_at', '<=', date('Y-m-d H:i:s'))->count(),
                'scheduled' => Post::where('published', 1)->where('published_at', '>', date('Y-m-d H:i:s'))->count(),
                'draft' => Post::where('published', 0)->count(),
            ]
        ]);
    }

    /**
     * AJAX Handler - Change Posts List
     *
     * @return void
     */
    public function onChangePostList()
    {
        $order = input('order');
        if (!array_key_exists($order, $this->getDefaultOrderOptions())) {
            $order = 'by_published';
        }

        $posts = $this->getPosts($order);
        return [
            'order' => $order,
            '#postsList' => $this->makePartial('posts', [
                'posts' => $posts
            ])
        ];
    }

}
