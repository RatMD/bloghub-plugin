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
use Markdown;
use RainLab\Blog\Models\Post;
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
                'default'           => 'created_at desc',
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
        $page = $this->property('pageNumber');
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
        $query = Comment::where('post_id', $this->post->id)
            ->limit($this->property('commentsPerPage'))
            ->offset($offset);
        
        // Pin Favorites
        if ($this->property('pinFavorites') !== '0' && $this->property('pinFavorites') !== false) {
            $query->orderBy('favorite DESC');
        }

        // Hide on Dislike
        if (($value = $this->property('hideOnDislike'))) {
            if (strpos($value, ':') === 0 && is_numeric(substr($value, 1))) {
                $val = substr($value, 1);
                $query->whereRaw("dislike / like >= $val");
            } else {
                $query->where('dislike', '<', $value);
            }
        }

        // Finish Query
        return $query->orderByRaw($order)->get()->toNested();
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
        } else {
            $this->prepareVars($post);

            if ($this->page['showCommentFormCaptcha']) {
                $builder = (new \Gregwar\Captcha\CaptchaBuilder)->build();
                Session::put('bloghubCaptchaPhrase', $builder->getPhrase());
                $this->page['captchaImage'] = $builder->inline();
            }

            $this->page['comments'] = $this->getComments();

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

        return hash_equals(
            Session::token(),
            $token
        );
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
     * Action - Reload Captcha
     *
     * @return mixed
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
     * Action - Like Comment
     *
     * @return mixed
     */
    public function onLike()
    {
        if (empty($post = $this->getPost())) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_post'));
        }
        $this->prepareVars($post);

        // Check if Like is enabled
        if ($this->config('like_comment') !== '1') {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.disabled_method'));
        }

        // Check if Comment exists
        if (empty($comment_id = input('comment_id'))) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.missing_comment_id'));
        }
        if (empty($comment = Comment::where('id', $comment_id)->first())) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.invalid_comment_id'));
        }

        // Check if current user can dislike
        if (!$this->page['currentUserCanLike']) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.not_allowed_to'));
        }

        // Add Like & Return
        if ($comment->like()) {
            return [
                'status' => 'success',
                'comment' => $this->renderPartial('@single', [
                    'comment' => $comment
                ])
            ];
        } else {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
        }
    }

    /**
     * Action - Dislike Comment
     *
     * @return mixed
     */
    public function onDislike()
    {
        if (empty($post = $this->getPost())) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_post'));
        }
        $this->prepareVars($post);

        // Check if Dislike is enabled
        if ($this->config('dislike_comment') !== '1') {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.disabled_method'));
        }

        // Check if Comment exists
        if (empty($comment_id = input('comment_id'))) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.missing_comment_id'));
        }
        if (empty($comment = Comment::where('id', $comment_id)->first())) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.invalid_comment_id'));
        }

        // Check if current user can dislike
        if (!$this->page['currentUserCanDislike']) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.not_allowed_to'));
        }

        // Add Dislike & Return
        if ($comment->dislike()) {
            return [
                'status' => 'success',
                'comment' => $this->renderPartial('@single', [
                    'comment' => $comment
                ])
            ];
        } else {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
        }
    }

    /**
     * Action - Favourite Comment
     *
     * @return mixed
     */
    public function onFavorite()
    {
        if (empty($post = $this->getPost())) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_post'));
        }
        $this->prepareVars($post);

        // Check if Favorite is enabled
        if ($this->config('author_favorites') !== '1') {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.disabled_method'));
        }

        // Check if Comment exists
        if (empty($comment_id = input('comment_id'))) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.missing_comment_id'));
        }
        if (empty($comment = Comment::where('id', $comment_id)->first())) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.invalid_comment_id'));
        }

        // Check if current user can favorite
        if (!$this->page['currentUserCanFavorite']) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.not_allowed_to'));
        }

        // Favorite Comment
        $comment->favorite = true;
        if ($comment->save()) {
            return [
                'status' => 'success',
            ];
        } else {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
        }
    }

    /**
     * Action - Approve Comment
     *
     * @return void
     */
    public function onApprove()
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

        // Check if current user is backend user
        if (!$this->page['currentUserIsBackend']) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.not_allowed_to'));
        }

        // Check if current user is allowed to moderatoe
        if (!$this->getBackendUser()->hasPermission('ratmd.bloghub.comments.moderate_comments')) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.no_permissions_for'));
        }

        // Reject Comment
        if ($comment->status === 'pending') {
            if ($comment->approve()) {
                return [
                    'status' => 'success',
                ];
            } else {
                throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
            }
        } else {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
        }
    }

    /**
     * Action - Reject Comment
     *
     * @return void
     */
    public function onReject()
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

        // Check if current user is backend user
        if (!$this->page['currentUserIsBackend']) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.not_allowed_to'));
        }

        // Check if current user is allowed to moderatoe
        if (!$this->getBackendUser()->hasPermission('ratmd.bloghub.comments.moderate_comments')) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.no_permissions_for'));
        }

        // Reject Comment
        if ($comment->status === 'pending') {
            if ($comment->reject()) {
                return [
                    'status' => 'success',
                ];
            } else {
                throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
            }
        } else {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_error'));
        }
    }

    /**
     * Action - Reply to Comment
     *
     * @return void
     */
    public function onReply()
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

        // Validate Form ID
        if (empty($formId = input('form_id'))) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.missing_form_id'));
        }

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
            '#' . $formId . '_reply' => $this->renderPartial('@comment-reply', [
                'comment' => $comment
            ]),
            'comment' => $comment,
            'submitButtonText' => Lang::get('ratmd.bloghub::lang.frontend.comments.submit_reply')
        ];
    }


    /**
     * Action - Cancel Reply to Comment
     *
     * @return void
     */
    public function onCancelReply()
    {
        if (empty($formId = input('form_id'))) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.missing_form_id'));
        }

        return [
            '#' . $formId . '_reply' => '',
            'submitButtonText' => Lang::get('ratmd.bloghub::lang.frontend.comments.submit_comment')
        ];
    }

    /**
     * Action - Write a new Comment or Reply
     *
     * @return mixed
     */
    public function onWriteComment()
    {
        if (empty($post = $this->getPost())) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.unknown_post'));
        }
        $this->prepareVars($post);

        // Validate Form ID
        if (empty($formId = input('comment_form_id'))) {
            throw new AjaxException(Lang::get('ratmd.bloghub::lang.frontend.errors.missing_form_id'));
        }

        // Validate CSRF Token
        if (!$this->verifyCsrfToken()) {
            throw new AjaxException([
                '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                    'type' => 'danger',
                    'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.invalid_csrf_token')
                ])
            ]);
        }

        // Validate Comment Validation Code
        $hasCaptcha = false;
        $hasHoneypot = false;
        if (!$this->verifyValidationCode(input('comment_validation'), input('comment_time'), $hasCaptcha, $hasHoneypot)) {
            throw new AjaxException([
                '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                    'type' => 'danger',
                    'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.invalid_validation_code')
                ])
            ]);
        }

        // Validate Comment Captcha
        if ($hasCaptcha) {
            if (!hash_equals(Session::get('bloghubCaptchaPhrase'), input('comment_captcha'))) {
                throw new AjaxException([
                    '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                        'type' => 'danger',
                        'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.invalid_captcha')
                    ])
                ]);
            }
        }

        // Validate Honeypot Field
        if ($hasHoneypot) {
            $honey = input('comment_honey');

            if (empty($honey) || !empty(input('comment_user')) || !empty(input('comment_email'))) {
                throw new AjaxException([
                    '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                        'type' => 'danger',
                        'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.honeypot_filled')
                    ])
                ]);
            }

            $honey = md5($honey);
        }

        // Check current User
        if (!$this->page['currentUserCanComment']) {
            throw new AjaxException([
                '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                    'type' => 'danger',
                    'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.not_allowed_to_commentd')
                ])
            ]);
        }

        // Validate Terms of Service
        if ($this->page['showCommentFormTos'] && input('comment_tos') !== '1') {
            throw new AjaxException([
                '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                    'type' => 'danger',
                    'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.tos_not_accepted')
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
                $comment->status = 'approved';
            }
        } else {
            $comment->author = isset($honey) ? input('comment_user' . $honey) : input('comment_user');
            $comment->author_email = isset($honey) ? input('comment_email' . $honey) : input('comment_email');
            $comment->author_uid = sha1(request()->ip());

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
                    '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                        'type' => 'danger',
                        'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.parent_not_found')
                    ])
                ]);
            }

            // Comment not on the same Post
            if ($parent->post_id !== $post->id) {
                throw new AjaxException([
                    '#' . $formId . '_alert' => $this->renderPartial('@alert', [
                        'type' => 'danger',
                        'message' => Lang::get('ratmd.bloghub::lang.frontend.errors.parent_invalid')
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
