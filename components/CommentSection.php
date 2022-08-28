<?php declare(strict_types=1);

namespace RatMD\BlogHub\Components;

use AjaxException;
use Lang;
use Config;
use Crypt;
use Request;
use Session;
use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use Cms\Classes\Theme;
use RainLab\Blog\Models\Post;
use RainLab\Pages\Classes\Page as RainLabPage;
use RatMD\BlogHub\Models\BlogHubSettings;
use RatMD\BlogHub\Models\Comment;
use System\Classes\PluginManager;

class CommentSection extends ComponentBase
{

    /**
     * Current Post
     *
     * @var ?Post
     */
    protected $post = null;

    /**
     * BlogHub Settings
     *
     * @var ?BlogHubSettings
     */
    protected $bloghubSettings = null;

    /**
     * Declare Component Details
     *
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'ratmd.bloghub::lang.components.comments_section.label',
            'description' => 'ratmd.bloghub::lang.components.comments_section.comment'
        ];
    }

    /**
     * Define Component Properties
     *
     * @return array
     */
    public function defineProperties()
    {
        return [
            'postSlug' => [
                'title'             => 'ratmd.bloghub::lang.components.comments_section.post_slug',
                'description'       => 'ratmd.bloghub::lang.components.comments_section.post_slug_comment',
                'type'              => 'dropdown',
                'default'           => '',
            ],
            'commentsPerPage' => [
                'title'             => 'ratmd.bloghub::lang.components.comments_section.comments_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'rainlab.blog::lang.settings.posts_per_page_validation',
                'default'           => '10',
            ],
            'pageNumber' => [
                'title'             => 'rainlab.blog::lang.settings.posts_pagination',
                'description'       => 'rainlab.blog::lang.settings.posts_pagination_description',
                'type'              => 'string',
                'default'           => '',
            ],
            'sortOrder' => [
                'title'             => 'ratmd.bloghub::lang.components.comments_section.comments_order',
                'description'       => 'ratmd.bloghub::lang.components.comments_section.comments_order_comment',
                'type'              => 'dropdown',
                'default'           => 'published_at desc',
            ],
            'commentsAnchor' => [
                'title'             => 'ratmd.bloghub::lang.components.comments_section.comments_anchor',
                'description'       => 'ratmd.bloghub::lang.components.comments_section.comments_anchor_comment',
                'type'              => 'string',
                'default'           => 'comments'
            ],
            'pinFavorites' => [
                'title'             => 'ratmd.bloghub::lang.components.comments_section.pin_favorites',
                'description'       => 'ratmd.bloghub::lang.components.comments_section.pin_favorites_comment',
                'type'              => 'checkbox',
                'default'           => '0'
            ],
            'disableForm' => [
                'title'             => 'ratmd.bloghub::lang.components.comments_section.disable_form',
                'description'       => 'ratmd.bloghub::lang.components.comments_section.disable_form_comment',
                'type'              => 'checkbox',
                'default'           => '0'
            ],
            'hideOnDislikes' => [
                'title'             => 'ratmd.bloghub::lang.components.comments_section.hide_on_dislike',
                'description'       => 'ratmd.bloghub::lang.components.comments_section.hide_on_dislike_comment',
                'type'              => 'string',
                'default'           => '0'
            ]
        ];
    }

    /**
     * Get BlogHub Settings
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function config(string $key)
    {
        if (empty($this->bloghubSettings)) {
            $this->bloghubSettings = BlogHubSettings::instance();
        }
        return $this->bloghubSettings->{$key} ?? BlogHubSettings::defaultValue($key);
    }

    /**
     * Get Sort Order Dropdown Options
     *
     * @return array
     */
    public function getSortOrderOptions()
    {
        return [
            'published_at DESC' => Lang::get('ratmd.bloghub::lang.sorting.published_at_desc'),
            'published_at ASC'  => Lang::get('ratmd.bloghub::lang.sorting.published_at_asc'),
            'likes DESC'        => Lang::get('ratmd.bloghub::lang.sorting.likes_desc'),
            'likes ASC'         => Lang::get('ratmd.bloghub::lang.sorting.likes_asc'),
            'dislikes DESC'     => Lang::get('ratmd.bloghub::lang.sorting.dislikes_desc'),
            'dislikes ASC'      => Lang::get('ratmd.bloghub::lang.sorting.dislikes_asc'),
        ];
    }

    /**
     * Get Current Post
     *
     * @return Post|null
     */
    protected function getPost()
    {
        $slug = $this->property('postSlug');
        if (empty($slug)) {
            if (!empty($component = $this->controller->getPage()->getComponent('blogPost'))) {
                $slug = $component->getProperties()['slug'];

                if (($last = strpos($slug, '}}')) > 0) {
                    if (($index = strpos($slug, ':')) > 0) {
                        $slug = trim(substr($slug, $index+1, $last-4));
                        $slug = $this->param($slug);
                    } else {
                        $slug = null;
                    }
                }
            }

            if (empty($slug)) {
                if (empty($slug = $this->param('slug'))) {
                    return null;
                }
            }
        }

        return Post::where('slug', $slug)->first();
    }

    /**
     * Get Comment List
     *
     * @return void
     */
    protected function getComments()
    {
        return Comment::where('post_id', $this->post->id)->getNested();
    }

    /**
     * Get Current User
     *
     * @return RainLab\User\Models\User|Backend\Models\User|null
     */
    protected function getCurrentUser()
    {
        if (($user = $this->getBackendUser()) !== null) {
            return $user;
        } else if (($user = $this->getFrontendUser()) !== null) {
            return $user;
        } else {
            return null;
        }
    }

    /**
     * Get Frontend User (when RainLab.User is installed)
     *
     * @return ?RainLab\User\Models\User
     */
    protected function getFrontendUser()
    {
        if (PluginManager::instance()->hasPlugin('RainLab.User')) {
            return null;
        }

        $rainLabAuth = \RainLab\User\Classes\AuthManager::instance();
        if ($rainLabAuth->check()) {
            return $rainLabAuth->getUser();
        } else {
            return null;
        }
    }

    /**
     * Get Backend User
     *
     * @return ?Backend\Models\User
     */
    protected function getBackendUser()
    {
        if (BackendAuth::check()) {
            return BackendAuth::getUser();
        } else {
            return null;
        }
    }


##
##  CHECK METHODS
##

    /**
     * Check if someone is logged in
     *
     * @return boolean
     */
    protected function isSomeoneLoggedIn()
    {
        return $this->getCurrentUser() !== null;
    }

    /**
     * Checks if the comment form is enabled on the current post.
     *
     * @return boolean
     */
    protected function isCommentFormEnabled()
    {

    }

    /**
     * Checks if the current user can comment on the current post.
     *
     * @return boolean
     */
    protected function canUserComment()
    {

    }
    


##
##  COMPONENT METHODS
##

    /**
     * Run Component
     *
     * @return mixed
     */
    public function onRun()
    {
        $this->post = $post = $this->getPost();

        if (empty($post) || (!empty($post) && $post->ratmd_bloghub_comment_visible === '0')) {
            $this->page['showComments'] = false;
            $this->page['showCommentsForm'] = false;
            $this->page['comments'] = null;
            $this->page['commentsMode'] = 'closed';
            $this->page['currentUser'] = null;
            $this->page['currentUserIsGuest'] = true;
            $this->page['currentUserIsFrontend'] = false;
            $this->page['currentUserIsBackend'] = false;
            $this->page['isLoggedIn'] = false;
            $this->page['currentUserCanLike'] = false;
            $this->page['currentUserCanDisike'] = false;
            $this->page['currentUserCanFavorite'] = false;
            $this->page['currentUserCanComment'] = false;
        } else {
            $this->prepareVars($post);
            $this->addJs('/plugins/ratmd/bloghub/assets/js/comments.js');
            $this->addCss('/plugins/ratmd/bloghub/assets/css/comments.css');
        }
    }

    /**
     * Prepare Page Variables
     *
     * @param Post $post
     * @return void
     */
    protected function prepareVars(Post $post)
    {
        $user = $this->getCurrentUser();
        $like = $this->config('like_comment') === '1';
        $dislike = $this->config('dislike_comment') === '1';
        $restrict = $this->config('restrict_to_users') === '1';
        $favorite = $this->config('author_favorites') === '1';

        // Show Comments List
        $this->page['showComments'] = true;
        
        // Set Comments Mode
        $this->page['commentsMode'] = $post->ratmd_bloghub_comment_mode;

        if ($this->property('disableForm') === '1') {
            $this->page['commentsMode'] = 'hidden';         // Disable Form [Page Property]
        }
        if ($this->config('guest_comments') === '0' && $this->page['commentsMode'] === 'public') {
            $this->page['commentsMode'] = 'restricted';     // Guests cannot comment [Settings]
        }

        // Set ShowCommentsForm
        if ($this->page['commentsMode'] === 'open') {
            $this->page['showCommentsForm'] = true;
        } else if ($this->page['commentsMode'] === 'restricted') {
            $this->page['showCommentsForm'] = $this->isSomeoneLoggedIn();
        } else if ($this->page['commentsMode'] === 'private') {
            $this->page['showCommentsForm'] = $this->getBackendUser() !== null;
        } else {
            $this->page['showCommentsForm'] = false;
        }

        // Set current user
        $this->page['currentUser'] = $user;
        $this->page['currentUserIsGuest'] = !$this->isSomeoneLoggedIn();
        $this->page['currentUserIsFrontend'] = $this->getFrontendUser() !== null;
        $this->page['currentUserIsBackend'] = $this->getBackendUser() !== null;
        $this->page['isLoggedIn'] = $this->isSomeoneLoggedIn();

        $this->page['currentUserCanComment'] = $this->page['showCommentsForm'];
        $this->page['currentUserCanLike'] = $like && (!$restrict || $this->page['isLoggedIn']);
        $this->page['currentUserCanDisike'] = $dislike && (!$restrict || $this->page['isLoggedIn']);
        $this->page['currentUserCanFavorite'] = $favorite && $user && $user->id === $this->post->user_id;
        
        // Skip when no comment form is shown
        if (!$this->page['currentUserCanComment']) {
            return;
        }

        // Comment Form Fields variables
        $this->page['showCommentFormTitle'] = $this->config('form_comment_title') === '1';
        $this->page['allowCommentFormMarkdown'] = $this->config('form_comment_markdown') === '1';
        $this->page['showCommentFormTos'] = $this->config('form_tos_checkbox') === '0';
        if ($this->page['showCommentFormTos'] && $this->config('form_tos_hide_on_user') === '1' && $this->page['isLoggedIn']) {
            $this->page['showCommentFormTos'] = false;
        } else {
            $this->page['commentFormTosLabel'] = BlogHubSettings::instance()->getTermsOfServiceLabel();
        }

        // Comment Form Captcha
        if ($this->config('form_comment_captcha') === '1') {

            $this->page['showCommentFormCaptcha'] = true;
        } else {
            
            $this->page['showCommentFormCaptcha'] = false;
        }

        // Comment Form Honeypot
        if ($this->config('form_comment_honeypot') === '1') {
            $time = time();
            $hash = md5(strval($time));

            $this->page['showCommentFormHoneypot'] = true;
            $this->page['honeypotUser'] = 'comment_user' . $hash;
            $this->page['honeypotEmail'] = 'comment_email' . $hash;
            $this->page['honeypotTime'] = $time;
        } else {
            $this->page['showCommentFormHoneypot'] = false;
        }
    }

    /**
     * Vertify CSRF and Session Token
     *
     * @return void
     */
    protected function verifyCsrfToken()
    {
        if (!Config::get('system.enable_csrf_protection', true)) {
            return true;
        }

        if (in_array(Request::method(), ['HEAD', 'GET', 'OPTIONS'])) {
            return true;
        }

        $token = Request::input('_token') ?: Request::header('X-CSRF-TOKEN');

        if (!$token && $header = Request::header('X-XSRF-TOKEN')) {
            $token = Crypt::decrypt($header, false);
        }

        if (!strlen($token) || !strlen(Session::token())) {
            return false;
        }

        return hash_equals(
            Session::token(),
            $token
        );
    }

    /**
     * Action - Like Comment
     *
     * @return mixed
     */
    public function onLike()
    {

    }

    /**
     * Action - Dislike Comment
     *
     * @return mixed
     */
    public function osDislike()
    {

    }

    /**
     * Action - Favourite Comment
     *
     * @return mixed
     */
    public function onFavorite()
    {

    }

    /**
     * Action - Accept Comment
     *
     * @return void
     */
    public function onAccept()
    {

    }

    /**
     * Action - Reject Comment
     *
     * @return void
     */
    public function onReject()
    {

    }

    /**
     * Action - Reply to Comment
     *
     * @return void
     */
    public function onReply()
    {
        if (empty($this->getPost())) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_post'));
        }

        $this->preparePost();
        $this->prepareVars();

        if (empty($formId = input('form_id'))) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.missing_form_id'));
        }

        if (!$this->isCommentFormEnabled()) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.form_disabled'));
        }
        
        if (!$this->userCanComment()) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.not_allowed_to_comment'));
        }

        return [
            '#' . $formId . '_reply' => 'Reply to',
            'submitButtonText' => Lang::get('ratmd.bloghub::lang.frontend.comments.submit_reply')
        ];
    }

    /**
     * Action - Write a new Comment or Reply
     *
     * @return mixed
     */
    public function onWriteComment()
    {
        $this->preparePost();
        $this->prepareVars();

        // Get current Form ID
        if (empty($formId = input('comment_form_id'))) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.missing_form_id'));
        }
        
        // CSRF Validation
        if (!$this->verifyCsrfToken()) {
            throw new AjaxException([
                '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                    'type' => 'danger',
                    'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.invalid_csrf_token')
                ])
            ]);
        }

        // Honeypot Validation
        $honey = input('comment_honey');
        if ($honey && strlen($honey) > 0) {
            if (!empty(input('comment_user')) || empty(input('comment_email'))) {
                throw new AjaxException([
                    '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                        'type' => 'danger',
                        'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.honeypot_filled')
                    ])
                ]);
            }
        }

        // Check Slug
        if (empty($this->slug)) {
            throw new AjaxException([
                '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                    'type' => 'danger',
                    'message' => 'The passed post slug is invalid or missing.'
                ])
            ]);
        }

        // Check Form Submission
        if (!$this->page['showCommentsForm']) {
            throw new AjaxException([
                '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                    'type' => 'danger',
                    'message' => 'You\'re not allowed to comment on this post.'
                ])
            ]);
        }

        // Validate Terms of Service
        if ($this->config('form_tos_checkbox') === '1' && !($this->config('form_tos_hide_on_user') && $this->isSomeoneLoggedIn())) {
            throw new AjaxException([
                '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                    'type' => 'danger',
                    'message' => 'You need to check the Term of Service to comment on this post.'
                ])
            ]);
        }

        // Validate Form Submission
        $comment = new Comment([
            'status' => 'pending',
            'content' => input('comment_comment')
        ]);

        if ($this->page['currentUser']) {
            $comment->authorable = $this->page['currentUser'];

            if ($this->config('moderate_user_comments') === '0' || $this->getBackendUser() !== null) {
                $comment->status = 'published';
            }
        } else {
            $comment->author = input('comment_user');
            $comment->author_email = input('comment_email');
            $comment->author_uid = sha1(request()->ip());

            if ($this->config('moderate_guest_comments') === '0') {
                $comment->status = 'published';
            }
        }

        // Set Comment Title
        if ($this->config('form_comment_title')) {
            $comment->title = input('comment_title');
        }

        // Set Related Post
        $comment->post = $this->post;
        
        // Validate Comment Parent
        $parentId = input('comment_parent');
        if (!empty ($parentId)) {
            if (empty($parent = Comment::where('id', $parentId)->first())) {
                throw new AjaxException([
                    '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                        'type' => 'danger',
                        'message' => 'The parent comment could not be found.'
                    ])
                ]);
            }
            $comment->parent_id = $parent->id;
        }

        return [
            'status' => $comment->save()
        ];

    }

}
