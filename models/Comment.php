<?php declare(strict_types=1);

namespace RatMD\BlogHub\Models;

use Lang;
use Markdown;
use Model;
use RatMD\BlogHub\Models\Visitor;

/**
 * Comment Model
 */
class Comment extends Model
{
    use \October\Rain\Database\Traits\SimpleTree;
    use \October\Rain\Database\Traits\Validation;

    /**
     * Table associated with this Model
     * 
     * @var string
     */
    public $table = 'ratmd_bloghub_comments';

    /**
     * Enable Modal Timestamps
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * Guarded Model attributes
     * 
     * @var array
     */
    protected $guarded = [
        '*'
    ];

    /**
     * Fillable Model attributes
     * 
     * @var array
     */
    protected $fillable = [
        "status",
        "title",
        "content",
        "favorite",
        "author",
        "author_email",
        "author_subscription",
        "parent_id",
        "author_id"
    ];

    /**
     * @var array rules for validation
     */
    public $rules = [
        'author' => 'nullable|string|min:3',
        'author_email' => 'nullable|email',
        'status' => 'required|in:pending,approved,rejected,spam',
        'content' => 'required|string|min:3'
    ];

    /**
     * @var array appends attributes to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

    /**
     * @var array hidden attributes removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * Mutable Date Attributes
     * 
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'approved_at',
        'rejected_at',
    ];

    /**
     * Define Belongs-To Relationships
     * 
     * @var array
     */
    public $belongsTo = [
        'post' => \RainLab\Blog\Models\Post::class,
        'parent' => Comment::class,
    ];

    /**
     * Define Has-Many Relationships
     * 
     * @var array
     */
    public $hasMany = [
        'children' => [
            Comment::class,
        ]
    ];

    /**
     * Define Morph-To Relationships
     * 
     * @var array
     */
    public $morphTo = [
        'authorable' => [
            'id' => 'author_id',
            'type' => 'author_table'
        ]
    ];

    /**
     * Get Status Options
     *
     * @return array
     */
    public function getStatusOptions()
    {
        return [
            'pending'   => Lang::get('ratmd.bloghub::lang.model.comments.statusPending'),
            'approved'  => Lang::get('ratmd.bloghub::lang.model.comments.statusApproved'),
            'rejected'  => Lang::get('ratmd.bloghub::lang.model.comments.statusRejected'),
            'spam'      => Lang::get('ratmd.bloghub::lang.model.comments.statusSpam')
        ];
    }

    /**
     * [HOOK] - Before Save Event Listener
     *
     * @return void
     */
    public function beforeSave()
    {
        if ($this->status === 'approved') {
            if (empty($this->approved_at)) {
                $this->approved_at = date('Y-m-d H:i:s');
            }
            if (!empty($this->rejected_at)) {
                $this->rejected_at = null;
            }
        }

        if ($this->status === 'rejected' || $this->status === 'spam') {
            if (empty($this->rejected_at)) {
                $this->rejected_at = date('Y-m-d H:i:s');
            }
            if (!empty($this->approved_at)) {
                $this->approved_at = null;
            }
        }
    }

    /**
     * [SETTER] Comment Content
     *
     * @return void
     */
    public function setContentAttribute(string $content)
    {
        $this->attributes['content'] = $content;
        $this->attributes['content_html'] = Markdown::parseClean(preg_replace('/(\S|^)\n(\S|$)/', '$1<br />$2', trim($content)));
    }

    /**
     * [GETTER] Comment Content, depending on the set option
     *
     * @return string
     */
    public function getCommentContentAttribute(): string
    {
        if (BlogHubSettings::get('form_comment_markdown', BlogHubSettings::defaultValue('form_comment_markdown')) === '1') {
            return Markdown::parseClean($this->content_html);
        } else {
            return Markdown::parseSafe(strip_tags($this->content));
        }
    }

    /**
     * [GETTER] Comment Content (deprecated version)
     * @deprecated 1.3.4 (Please use comment_content instead), will be removed in 1.5.0!
     * 
     * @return string
     */
    public function getRenderContentAttribute(): string
    {
        return $this->getCommentContentAttribute();
    }

    /**
     * [GETTER] Get Author [GR]avatar
     *
     * @param int $size
     * @return string
     */
    public function getAvatarAttribute($size = 80)
    {
        if (!empty($this->author_email)) {
            $email = md5(strtolower($this->author_email ?? 'none'));
        } else if ($this->author_id) {
            if ($this->author_table === 'Backend\Models\User') {
                return $this->authorable->getAvatarThumb($size);
            } else {
                $email = md5(strtolower($this->authorable->email ?? 'none'));
            }
        } else {
            $email = md5('none');
        }

        return 'https://www.gravatar.com/avatar/' . $email . '?s='. $size .'&d=mp';
    }

    /**
     * [GETTER] Authors display name, depending on the author type
     *
     * @return string
     */
    public function getDisplayNameAttribute(): string
    {
        if (!empty($this->author)) {
            return $this->author;
        } else if ($this->author_id) {
            if ($this->author_table === 'Backend\Models\User') {
                return $this->authorable->bloghub_display();
            } else {
                return $this->authorable->username;
            }
        } else {
            return trans('ratmd.bloghub::lang.model.comments.guest');
        }
    }

    /**
     * [GETTER] Formatted published ago date/time.
     *
     * @return string
     */
    public function getPublishedAgoAttribute(): string
    {
        $seconds = (time() - $this->created_at->getTimestamp());

        if ($seconds >= 2419200) {
            return date('F, j. Y - H:i', $this->created_at->getTimestamp());
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
            return trans('ratmd.bloghub::lang.model.comments.seconds_ago');
        }

        return trans('ratmd.bloghub::lang.model.comments.x_ago', [
            'amount' => $amount,
            'format' => trans('ratmd.bloghub::lang.model.post.published_format_' . $format)
        ]);
    }

    /**
     * [GETTER] Check if current user already liked this comment.
     *
     * @return boolean
     */
    public function getCurrentLikesAttribute(): bool
    {
        $visitor = Visitor::currentUser();
        return $visitor->getCommentVote($this->id) === 'like';
    }

    /**
     * [GETTER] Check if current user already disliked this comment.
     *
     * @return boolean
     */
    public function getCurrentDislikesAttribute(): bool
    {
        $visitor = Visitor::currentUser();
        return $visitor->getCommentVote($this->id) === 'dislike';
    }

    /**
     * [ACTION] Like a Comment
     *
     * @return bool
     */
    public function like(): bool
    {
        $visitor = Visitor::currentUser();

        $vote = $visitor->getCommentVote($this->id);
        if ($vote === 'like') {
            return true;
        } else if ($vote === 'dislike') {
            if (!$visitor->removeCommentDislike($this->id)) {
                return false;
            }
            $this->dislikes = $this->dislikes - 1;
        }

        if ($visitor->addCommentLike($this->id)) {
            $this->likes = $this->likes + 1;
            return $this->save();
        } else {
            return false;
        }
    }

    /**
     * [ACTION] Dislike A Comment
     *
     * @return bool
     */
    public function dislike()
    {
        $visitor = Visitor::currentUser();

        $vote = $visitor->getCommentVote($this->id);
        if ($vote === 'dislike') {
            return true;
        } else if ($vote === 'like') {
            if (!$visitor->removeCommentLike($this->id)) {
                return false;
            }
            $this->likes = $this->likes - 1;
        }

        if ($visitor->addCommentDislike($this->id)) {
            $this->dislikes = $this->dislikes + 1;
            return $this->save();
        } else {
            return false;
        }
    }

    /**
     * [ACTION] Change comment status
     *
     * @param string $status
     * @return boolean
     */
    public function changeStatus(string $status): bool
    {
        $this->status = $status;
        $this->approved_at = $status === 'approved' ? date("Y-m-d H:i:s") : null;
        $this->rejected_at = $status !== 'approved' ? date("Y-m-d H:i:s") : null;
        return $this->save();
    }

    /**
     * [ACTION] Approve
     * 
     * @return boolean
     */
    public function approve(): bool
    {
        return $this->changeStatus('approved');
    }
    
    /**
     * [ACTION] Reject Comment
     * 
     * @return boolean
     */
    public function reject(): bool
    {
        return $this->changeStatus('rejected');
    }

    /**
     * [ACTION] Mark Comment as spam
     * 
     * @return boolean
     */
    public function spam(): bool
    {
        return $this->changeStatus('spam');
    }

}
