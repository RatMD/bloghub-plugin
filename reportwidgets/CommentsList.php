<?php declare(strict_types=1);

namespace RatMD\BlogHub\ReportWidgets;

use AjaxException;
use BackendAuth;
use Lang;
use Backend\Classes\ReportWidgetBase;
use Cms\Classes\Controller;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use RatMD\BlogHub\Models\Comment;
use System\Classes\UpdateManager;

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
            'postPage' => [
                'title'         => 'rainlab.blog::lang.settings.posts_post',
                'description'   => 'rainlab.blog::lang.settings.posts_post_description',
                'type'          => 'dropdown',
                'default'       => 'blog/post',
            ],
            'defaultTab' => [
                'title'         => 'ratmd.bloghub::lang.components.comments_list.default_tab',
                'description'   => 'ratmd.bloghub::lang.components.comments_list.default_tab_comment',
                'type'          => 'dropdown',
                'options'       => [
                    'pending'   => Lang::get('ratmd.bloghub::lang.model.comments.statusPending'),
                    'accepted'  => Lang::get('ratmd.bloghub::lang.model.comments.statusAccepted'),
                    'rejected'  => Lang::get('ratmd.bloghub::lang.model.comments.statusRejected'),
                    'spam'      => Lang::get('ratmd.bloghub::lang.model.comments.statusSpam')
                ]
            ],
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
     * Adds widget specific asset files. Use $this->addJs() and $this->addCss()
     * to register new assets to include on the page.
     * 
     * @return void
     */
    protected function loadAssets()
    {
        $this->addCss('/plugins/ratmd/bloghub/assets/css/widget-commentslist.css');
        if (version_compare(UpdateManager::instance()->getCurrentVersion(), '3.0.0', '<')) {
            $this->addCss('/plugins/ratmd/bloghub/assets/css/widget-octoberv2.css');
        }
    }

    /**
     * Renders the widget's primary contents.
     * 
     * @return string HTML markup supplied by this widget.
     */
    public function render()
    {
        $defaultTab = $this->property('defaultTab', 'pending');
        if (!in_array($defaultTab, ['pending', 'approved', 'rejected', 'spam'])) {
            $defaultTab = 'pending';
        }

        $comments = Comment::where('status', $defaultTab)->orderBy('created_at', 'DESC')->limit(6)->get();
        if ($comments->count() === 0) {
            $comment = null;
        } else {
            if (!empty($postPage = $this->property('postPage'))) {
                $comments->each(fn ($item) => $item->post->setUrl($postPage, new Controller(Theme::getActiveTheme())));
            }
            $comment = $comments->shift();
        }

        return $this->makePartial('widget', [
            'status' => $defaultTab,
            'counts' => [
                'pending' => Comment::where('status', 'pending')->count(),
                'approved' => Comment::where('status', 'approved')->count(),
                'rejected' => Comment::where('status', 'rejected')->count(),
                'spam' => Comment::where('status', 'spam')->count(),
            ],

            'comment' => $comment,
            'commentPartial' => $this->makePartial('comment', [
                'comment' => $comment
            ]),

            'list' => $comments,
            'listPartial' => $this->makePartial('list', [
                'list' => $comments
            ])
        ]);
    }

    /**
     * AJAX Handler - Change Comments List Tab
     *
     * @return array
     */
    public function onChangeTab()
    {
        $tab = input('tab');
        if (!in_array($tab, ['pending', 'approved', 'rejected', 'spam'])) {
            $tab = 'pending';
        }

        // Load Comments
        $comments = Comment::where('status', $tab)->orderBy('created_at', 'DESC')->limit(6)->get();
        if ($comments->count() === 0) {
            $comment = null;
        } else {
            if (!empty($postPage = $this->property('postPage'))) {
                $comments->each(fn ($item) => $item->post->setUrl($postPage, new Controller(Theme::getActiveTheme())));
            }
            $comment = $comments->shift();
        }

        // Return Response
        return [
            'tab' => $tab,
            '#commentPartial' => $this->makePartial('comment', [
                'comment' => $comment
            ]),
            '#listPartial' => $this->makePartial('list', [
                'list' => $comments
            ])
        ];
    }

    /**
     * AJAX Handler - Change Focued Comment
     *
     * @return array
     */
    public function onChangeComment()
    {
        $commentId = intval(input('comment_id'));
        
        // Load Single Comment
        $comment = Comment::where('id', $commentId)->first();
        if ($comment) {
            if (!empty($postPage = $this->property('postPage'))) {
                $comment->post->setUrl($postPage, new Controller(Theme::getActiveTheme()));
            }
        }

        // Load Comments
        $comments = Comment::where('status', empty($comment) ? 'unknown' : $comment->status)
            ->where('id', '!=', $commentId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)->get();

        // Return Response
        return [
            '#commentPartial' => $this->makePartial('comment', [
                'comment' => $comment
            ]),
            '#listPartial' => $this->makePartial('list', [
                'list' => $comments
            ])
        ];
    }

    /**
     * AJAX Handler - Change Comment Status
     *
     * @return array
     */
    public function onChangeStatus()
    {
        $status = input('status');
        $comment_id = input('comment_id');

        // Check if user has Permission
        if(!(BackendAuth::check() && BackendAuth::getUser()->hasPermission('ratmd.bloghub.comments.moderate_comments'))) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.moderate_permission'));
        }

        // Validate Status
        if (!in_array($status, ['approve', 'reject', 'spam'])) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.invalid_status'));
        }

        // Validate Comment ID
        if (empty($comment = Comment::where('id', $comment_id)->first())) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_status'));
        }
        
        // Update Status
        if (($status = $comment->{$status}()) === false) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
        }

        // Rebuild Comments List
        $comments = Comment::where('status', 'pending')->orderBy('created_at', 'DESC')->limit(6)->get();
        if ($comments->count() === 0) {
            $comment = null;
        } else {
            if (!empty($postPage = $this->property('postPage'))) {
                $comments->each(fn ($item) => $item->post->setUrl($postPage, new Controller(Theme::getActiveTheme())));
            }
            $comment = $comments->shift();
        }

        // Return Response
        return [
            'status' => Lang::get('ratmd.bloghub::lang.frontend.success.update_status'),
            'counts' => [
                'pending' => Comment::where('status', 'pending')->count(),
                'approved' => Comment::where('status', 'approved')->count(),
                'rejected' => Comment::where('status', 'rejected')->count(),
                'spam' => Comment::where('status', 'spam')->count(),
            ],
            '#commentPartial' => $this->makePartial('comment', [
                'comment' => $comment
            ]),
            '#listPartial' => $this->makePartial('list', [
                'list' => $comments
            ])
        ];
    }

}
