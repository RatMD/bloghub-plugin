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
use Illuminate\Pagination\LengthAwarePaginator;
use RainLab\Blog\Models\Post;
use RatMD\BlogHub\Models\BlogHubSettings;
use RatMD\BlogHub\Models\Comment;
use RatMD\BlogHub\Models\Visitor;
use System\Classes\PluginManager;
use System\Classes\UpdateManager;
use System\Classes\VersionManager;
use System\Models\PluginVersion;

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
                'default'           => 'created_at desc',
            ],
            'commentHierarchy' => [
                'title'             => 'ratmd.bloghub::lang.components.comments_section.comments_hierarchy',
                'description'       => 'ratmd.bloghub::lang.components.comments_section.comments_hierarchy_comment',
                'type'              => 'checkbox',
                'default'           => '1'
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
            'hideOnDislikes' => [
                'title'             => 'ratmd.bloghub::lang.components.comments_section.hide_on_dislike',
                'description'       => 'ratmd.bloghub::lang.components.comments_section.hide_on_dislike_comment',
                'type'              => 'string',
                'default'           => '0'
            ],
            'formPosition' => [
                'group'             => 'ratmd.bloghub::lang.components.comments_section.group_form',
                'title'             => 'ratmd.bloghub::lang.components.comments_section.form_position',
                'description'       => 'ratmd.bloghub::lang.components.comments_section.form_position_comment',
                'type'              => 'dropdown',
                'default'           => 'above',
                'useSearch'         => false,
                'options'           => [
                    'above'             => 'ratmd.bloghub::lang.components.comments_section.form_position_above',
                    'below'             => 'ratmd.bloghub::lang.components.comments_section.form_position_below',
                ]
            ],
            'disableForm' => [
                'group'             => 'ratmd.bloghub::lang.components.comments_section.group_form',
                'title'             => 'ratmd.bloghub::lang.components.comments_section.disable_form',
                'description'       => 'ratmd.bloghub::lang.components.comments_section.disable_form_comment',
                'type'              => 'checkbox',
                'default'           => '0'
            ],
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
            'created_at DESC'   => Lang::get('ratmd.bloghub::lang.sorting.created_at_desc'),
            'created_at ASC'    => Lang::get('ratmd.bloghub::lang.sorting.created_at_asc'),
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
        $page = empty($this->property('pageNumber')) ? intval(get('cpage')) : intval($this->property('pageNumber'));
        $limit = $this->property('commentsPerPage');
        if ($page > 1) {
            $offset = ($page-1) * $this->property('commentsPerPage');
        } else {
            $offset = 0;
        }

        $order = $this->property('sortOrder');
        if (!array_key_exists($order, $this->getSortOrderOptions())) {
            $order = 'created_at DESC';
        }

        // Start Query
        $query = Comment::where('post_id', $this->post->id);
        
        // Check Permissions
        if ($this->page['currentUserCanModerate']) {
            $query->whereIn('status', ['approved', 'pending']);
        } else {
            $query->where(function($builder) {
                $builder->where('status', 'approved')->orWhere(function ($builder) {
                    $builder->where('status', 'pending')
                        ->where('author_id', Visitor::currentUser()->id)
                        ->where('author_table', 'RatMD\\BlogHub\\Models\\Visitor');
                });
            });
        }
        
        // Pin Favorites
        if ($this->property('pinFavorites') === '1') {
            $query->orderByDesc('favorite');
        }
        $orders = explode(' ', $order);
        $query->orderBy($orders[0], strtoupper($orders[1]) === 'DESC'? 'DESC': 'ASC');

        // Hide on Dislike
        if (($value = $this->property('hideOnDislikes')) !== '0') {
            if (strpos($value, ':') === 0 && is_numeric(substr($value, 1))) {
                $val = substr($value, 1);
                $query->whereRaw("(dislikes == 0 OR dislikes / likes < $val)");
            } else {
                $query->where('dislikes', '<', $value);
            }
        }

        // Finish Query
        $result = $query->get();
        if ($this->page['showCommentsHierarchical']) {
            $result = $result->toNested();
        }

        $pageName = $this->getPage()->getBaseFileName();
        return new LengthAwarePaginator(
            $result->slice($offset, $offset+$limit), 
            $result->count(), 
            $limit, 
            $page,
            [
                'path' => $this->controller->pageUrl($pageName, ['slug' => $this->post->slug]), 
                'fragment' => $this->property('commentAnchor') ?? 'comments',
                'pageName' =>'cpage'
            ]
        );
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
        $versionManager = VersionManager::instance();

        if (PluginManager::instance()->hasPlugin('RainLab.User')) {
            $user = false;
            if (version_compare($versionManager->getLatestVersion('RainLab.User'), '3.0.0', '>=')) {
                $user = app()->resolved('auth') && \Auth::check() ? \Auth::user() : null;
            } else {
                $rainAuth = \RainLab\User\Classes\AuthManager::instance();
                $user = $rainAuth->check() ? $rainAuth->getUser() : null;
            }
            return $user;
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
            $this->page['showCommentsHierarchical'] = $this->property('commentHierarchy') === '1';
            $this->page['comments'] = null;
            $this->page['commentsFormPosition'] = $this->property('formPosition');
            $this->page['commentsMode'] = 'closed';
            $this->page['currentUser'] = null;
            $this->page['currentUserIsGuest'] = true;
            $this->page['currentUserIsFrontend'] = false;
            $this->page['currentUserIsBackend'] = false;
            $this->page['isLoggedIn'] = false;
            $this->page['currentUserCanLike'] = false;
            $this->page['currentUserCanDislike'] = false;
            $this->page['currentUserCanFavorite'] = false;
            $this->page['currentUserCanComment'] = false;
            $this->page['currentUserCanModerate'] = false;
        } else {
            $this->prepareVars($post);

            if ($this->page['showCommentFormCaptcha']) {
                $builder = (new \Gregwar\Captcha\CaptchaBuilder)->build();
                Session::put('bloghubCaptchaPhrase', $builder->getPhrase());
                $this->page['captchaImage'] = $builder->inline();
            }

            $this->page['comments'] = $this->getComments();

            if (version_compare(UpdateManager::instance()->getCurrentVersion(), '3.0.0', '<')) {
                $this->addJs('/plugins/ratmd/bloghub/assets/js/comments-legacy.js');
            } else {
                $this->addJs('/plugins/ratmd/bloghub/assets/js/comments.js');
            }
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
        $this->page['showCommentsHierarchical'] = $this->property('commentHierarchy') === '1';
        $this->page['commentsFormPosition'] = $this->property('formPosition');
        if ($this->property('disableForm') === '1') {
            $this->page['commentsMode'] = 'hidden';
            $this->page['showCommentsForm'] = false;
        } else {
            $this->page['showCommentsForm'] = true;
        }
        
        // Set Comments Mode
        $this->page['commentsMode'] = $post->ratmd_bloghub_comment_mode;
        if ($this->config('guest_comments') === '0' && $this->page['commentsMode'] === 'public') {
            $this->page['commentsMode'] = 'restricted';     // Guests cannot comment [Settings]
        }

        // Set currentUserCanComment
        if ($this->page['commentsMode'] === 'open') {
            $this->page['currentUserCanComment'] = true;
        } else if ($this->page['commentsMode'] === 'restricted') {
            $this->page['currentUserCanComment'] = $this->isSomeoneLoggedIn();
        } else if ($this->page['commentsMode'] === 'private') {
            $this->page['currentUserCanComment'] = $this->getBackendUser() !== null;
        } else if ($this->page['commentsMode'] === 'closed') {
            $this->page['currentUserCanComment'] = false;
        } else {
            $this->page['showCommentsForm'] = false;
            $this->page['currentUserCanComment'] = false;
        }

        // Set current user
        $this->page['currentUser'] = $user;
        $this->page['currentUserIsGuest'] = !$this->isSomeoneLoggedIn();
        $this->page['currentUserIsFrontend'] = $this->getFrontendUser() !== null;
        $this->page['currentUserIsBackend'] = $this->getBackendUser() !== null;
        $this->page['isLoggedIn'] = $this->isSomeoneLoggedIn();

        $this->page['currentUserCanLike'] = $like && (!$restrict || $this->page['isLoggedIn']);
        $this->page['currentUserCanDislike'] = $dislike && (!$restrict || $this->page['isLoggedIn']);
        $this->page['currentUserCanFavorite'] = $favorite && $user && intval($user->id) === intval($post->user_id);
        $this->page['currentUserCanModerate'] = $this->page['currentUserIsBackend'] && $user && $user->hasPermission('ratmd.bloghub.comments.moderate_comments');
        
        // Skip when no comment form is shown
        if (!$this->page['currentUserCanComment']) {
            return;
        }

        // Comment Form Fields variables
        $this->page['showCommentFormTitle'] = $this->config('form_comment_title') === '1';
        $this->page['allowCommentFormMarkdown'] = $this->config('form_comment_markdown') === '1';
        $this->page['showCommentFormTos'] = $this->config('form_tos_checkbox') === '1';
        if ($this->page['showCommentFormTos'] && $this->config('form_tos_hide_on_user') === '1' && $this->page['isLoggedIn']) {
            $this->page['showCommentFormTos'] = false;
        } else {
            $this->page['commentFormTosLabel'] = BlogHubSettings::instance()->getTermsOfServiceLabel();
        }

        // Comment Form Captcha
        if ($this->config('form_comment_captcha') === '1' && !$this->page['isLoggedIn']) {
            $hasCaptcha = true;
            $this->page['showCommentFormCaptcha'] = true;
        } else {
            $hasCaptcha = false;
            $this->page['showCommentFormCaptcha'] = false;
        }

        // Comment Form Honeypot
        if ($this->config('form_comment_honeypot') === '1') {
            $hasHoneypot = true;
            $time = time();
            $hash = md5(strval($time));

            $this->page['showCommentFormHoneypot'] = true;
            $this->page['honeypotUser'] = 'comment_user' . $hash;
            $this->page['honeypotEmail'] = 'comment_email' . $hash;
            $this->page['honeypotTime'] = $time;
        } else {
            $hasHoneypot = false;
            $this->page['showCommentFormHoneypot'] = false;
        }

        // Validation fields
        $this->page['validationTime'] = time();
        $this->page['validationHash'] = hash_hmac(
            'SHA256', 
            strval($this->page['validationTime']), 
            strval(0 + ($hasCaptcha ? 10 : 0) + ($hasHoneypot ? 5 : 0))
        );
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

        return hash_equals(Session::token(), $token);
    }

    /**
     * Verify Comment Validation Code
     *
     * @param string $code
     * @param string $time
     * @param boolean $hasCaptcha
     * @param boolean $hasHoneypot
     * @return boolean
     */
    protected function verifyValidationCode(string $code, string $time, bool &$hasCaptcha, bool &$hasHoneypot)
    {
        if (hash_equals(hash_hmac('SHA256', $time, '0'), $code)) {
            return true;
        }

        if (hash_equals(hash_hmac('SHA256', $time, '5'), $code)) {
            $hasHoneypot = true;
            return true;
        }

        if (hash_equals(hash_hmac('SHA256', $time, '10'), $code)) {
            $hasCaptcha = true;
            return true;
        }

        if (hash_equals(hash_hmac('SHA256', $time, '15'), $code)) {
            $hasCaptcha = true;
            $hasHoneypot = true;
            return true;
        }

        return false;
    }

    /**
     * Validate AJAX Method
     * 
     * @return Comment
     */
    protected function validateAjaxMethod()
    {
        if (empty($post = $this->getPost())) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_post'));
        }
        $this->prepareVars($post);

        // Check if Comment exists
        if (empty($comment_id = input('comment_id'))) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.missing_comment_id'));
        }
        if (empty($comment = Comment::where('id', $comment_id)->first())) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.invalid_comment_id'));
        }

        // Return Comment
        return $comment;
    }

    /**
     * AJAX Handler - Change Comment Status (approve, reject, spam, favorite)
     *
     * @return array
     */
    public function onChangeStatus()
    {
        $comment = $this->validateAjaxMethod();

        // Get new Status
        if (empty($status = input('status')) || !in_array($status, ['favorite', 'approve', 'reject', 'spam'])) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
        }

        // Favorite Comment
        if ($status === 'favorite') {

            // Check if Favorite is enabled
            if ($this->config('author_favorites') !== '1') {
                throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.disabled_method'));
            }
    
            // Check if current user can favorite
            if (!$this->page['currentUserCanFavorite']) {
                throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.not_allowed_to'));
            }

            // Set Data
            $comment->favorite = !$comment->favorite;
            $result = $comment->save();
        }

        // Moderate Comment
        else if ($status === 'approve' || $status === 'reject' || $status === 'spam') {

            // Check if current user is backend user
            if (!$this->page['currentUserIsBackend']) {
                throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.not_allowed_to'));
            }
    
            // Check if current user is allowed to moderatoe
            if (!$this->page['currentUserCanModerate']) {
                throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.no_permissions_for'));
            }

            // Check if State can be changed (Frontend Moderation is limited to pending comments)
            if ($comment->status !== 'pending') {
                throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
            }
            $result = $comment->{$status}();
        }

        // Return Result
        if (isset($result) && $result) {
            return [
                'status' => 'success',
                'comment' => in_array($status, ['reject', 'spam']) ? null : $this->renderPartial('@_single', [
                    'comment' => $comment
                ])
            ];
        } else {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
        }
    }

    /**
     * AJAX Handler - Change Comment Vote (like, dislike)
     *
     * @return array
     */
    public function onVote()
    {
        $comment = $this->validateAjaxMethod();

        // Get new Vote
        if (empty($vote = input('vote')) || !in_array($vote, ['like', 'dislike'])) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
        }

        // Check if Like or dislike is enabled
        if ($this->config($vote . '_comment') !== '1') {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.disabled_method'));
        }

        // Check if current user can Like or Dislike
        if (!$this->page['currentUserCan' . ucfirst($vote)]) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.not_allowed_to'));
        }

        // Add Vote & Return
        if ($comment->{$vote}()) {
            return [
                'status' => 'success',
                'comment' => $this->renderPartial('@_single', [
                    'comment' => $comment
                ])
            ];
        } else {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
        }
    }

    /**
     * AJAX Handler - Create a new Reply
     *
     * @return array
     */
    public function onCreateReply()
    {
        $comment = $this->validateAjaxMethod();

        // Check if form is disabled
        if (!$this->page['showCommentsForm']) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.form_disabled'));
        }

        // Check if current user can comment
        if (!$this->page['currentUserCanComment']) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.not_allowed_to_comment'));
        }

        // Return Reply Partial and submit button text
        return [
            'status' => 'success',
            'reply' => $this->renderPartial('@_reply', [
                'comment' => $comment
            ]),
            'comment' => $comment,
            'submitText' => Lang::get('ratmd.bloghub::lang.frontend.comments.submit_reply')
        ];
    }

    /**
     * AJAX Handler - Cancel current Reply
     *
     * @return array
     */
    public function onCancelReply()
    {
        return [
            'submitText' => Lang::get('ratmd.bloghub::lang.frontend.comments.submit_comment')
        ];
    }
    
    /**
     * AJAX Handler - Reload Captcha
     *
     * @return array
     */
    public function onReloadCaptcha()
    {
        $builder = (new \Gregwar\Captcha\CaptchaBuilder)->build();
        Session::put('bloghubCaptchaPhrase', $builder->getPhrase());

        // Send new Image
        return [
            'captchaImage' => $builder->inline()
        ];
    }

    /**
     * AJAX Handler - Write a new Comment or Reply
     *
     * @return mixed
     */
    public function onComment()
    {
        if (empty($post = $this->getPost())) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_post'));
        }
        $this->prepareVars($post);

        // Validate CSRF Token
        if (!$this->verifyCsrfToken()) {
            throw new AjaxException([
                'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.invalid_csrf_token')
            ]);
        }

        // Validate Comment Validation Code
        $hasCaptcha = false;
        $hasHoneypot = false;
        if (!$this->verifyValidationCode(input('comment_validation'), input('comment_time'), $hasCaptcha, $hasHoneypot)) {
            throw new AjaxException([
                'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.invalid_validation_code')
            ]);
        }

        // Validate Comment Captcha
        if ($hasCaptcha) {
            if (strtoupper(Session::get('bloghubCaptchaPhrase')) !== strtoupper(input('comment_captcha'))) {
                $builder = (new \Gregwar\Captcha\CaptchaBuilder)->build();
                Session::put('bloghubCaptchaPhrase', $builder->getPhrase());

                throw new AjaxException([
                    'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.invalid_captcha') . Session::get('bloghubCaptchaPhrase'),
                    'captchaImage' => $builder->inline()
                ]);
            }
        }

        // Validate Honeypot Field
        if ($hasHoneypot) {
            $honey = input('comment_honey');

            if (empty($honey) || !empty(input('comment_user')) || !empty(input('comment_email'))) {
                throw new AjaxException([
                    'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.honeypot_filled')
                ]);
            }

            $honey = md5($honey);
        }

        // Check current User
        if (!$this->page['currentUserCanComment']) {
            throw new AjaxException([
                'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.not_allowed_to_commentd')
            ]);
        }

        // Validate Terms of Service
        if ($this->page['showCommentFormTos'] && input('comment_tos') !== '1') {
            throw new AjaxException([
                'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.tos_not_accepted')
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
                $comment->status = 'approved';
            }
        } else {
            $comment->author = isset($honey) ? input('comment_user' . $honey) : input('comment_user');
            $comment->author_email = isset($honey) ? input('comment_email' . $honey) : input('comment_email');
            $comment->author_uid = sha1(request()->ip());
            $comment->authorable = Visitor::currentUser();

            if ($this->config('moderate_guest_comments') === '0') {
                $comment->status = 'approved';
            }
        }

        // Set Comment Title
        if ($this->config('form_comment_title')) {
            $comment->title = input('comment_title');
        }

        // Set Related Post
        $comment->post = $post;
        
        // Validate Comment Parent
        $parentId = input('comment_parent');
        if (!empty($parentId)) {

            // Comment ID unknown
            if (empty($parent = Comment::where('id', $parentId)->first())) {
                throw new AjaxException([
                    'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.parent_not_found')
                ]);
            }

            // Comment not on the same Post
            if (intval($parent->post_id) !== intval($post->id)) {
                throw new AjaxException([
                    'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.parent_invalid')
                ]);
            }

            $comment->parent_id = $parent->id;
        }

        if ($comment->save()) {
            $this->post = $this->getPost();

            if ($this->page['showCommentFormCaptcha']) {
                $builder = (new \Gregwar\Captcha\CaptchaBuilder)->build();
                Session::put('bloghubCaptchaPhrase', $builder->getPhrase());
                $this->page['captchaImage'] = $builder->inline();
            }
            $this->page['comments'] = $this->getComments();

            return [
                'status' => 'success',
                'comments' => $this->renderPartial('@default')
            ];
        } else {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
        }
    }

}
