<?php declarE(strict_types=1);

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
            'pending' => Lang::get('ratmd.bloghub::lang.model.comments.statusPending'),
            'approved' => Lang::get('ratmd.bloghub::lang.model.comments.statusApproved'),
            'rejected' => Lang::get('ratmd.bloghub::lang.model.comments.statusRejected'),
            'spam' => Lang::get('ratmd.bloghub::lang.model.comments.statusSpam'),
        ];
    }

    /**
     * Before Save Event Listener
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

        if ($this->status === 'rejected') {
            if (empty($this->rejected_at)) {
                $this->rejected_at = date('Y-m-d H:i:s');
            }
            if (!empty($this->approved_at)) {
                $this->approved_at = null;
            }
        }
    }

    /**
     * Set Comment Attribute
     *
     * @return void
     */
    public function setContentAttribute(string $content)
    {
        $this->attributes['content']= $content;
        $this->attributes['content_html'] = Markdown::parse($content);
    }

    /**
     * Get Content Attribute
     *
     * @return string
     */
    public function getContentAttribute()
    {
        if (BlogHubSettings::get('form_comment_markdown', '1') === '1') {
            return $this->content_html;
        } else {
            return $this->content;
        }
    }

    /**
     * Get Author [GR]avatar
     *
     * @param int $size
     * @return string
     */
    public function avatar($size = 80)
    {
        if ($this->author_id) {
            if ($this->author_table === 'Backend\Models\User') {
                return $this->authorable->getAvatarThumb($size);
            } else {
                $email = md5(strtolower($this->authorable->email));
            }
        } else {
            $email = md5(strtolower($this->author_email));
        }

        return 'https://www.gravatar.com/avatar/' . $email . '?s='. $size .'&d=mp';
    }

    /**
     * Get Author Display Name, depending on the author type
     *
     * @return mixed
     */
    public function display_name()
    {
        if ($this->author_id) {
            if ($this->author_table === 'Backend\Models\User') {
                return $this->authorable->bloghub_display();
            } else {
                return $this->authorable->username;
            }
        } else {
            return $this->author;
        }
    }

    /**
     * Get formatted published ago timestamp.
     *
     * @return mixed
     */
    public function published_ago()
    {
        $seconds = (time() - $this->created_at->getTimestamp());

        if ($seconds >= 2419200) {
            return date('F, j. Y - H:i', $this->getTimestamp());
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
     * Check if current user already liked this comment
     *
     * @return void
     */
    public function current_likes()
    {
        $visitor = Visitor::currentUser();
        return $visitor->getCommentVote($this->id) === 'like';
    }

    /**
     * Check if current user already disliked this comment
     *
     * @return void
     */
    public function current_dislikes()
    {
        $visitor = Visitor::currentUser();
        return $visitor->getCommentVote($this->id) === 'dislike';
    }

    /**
     * Like a Comment
     *
     * @return bool
     */
    public function like()
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
     * Dislike A Comment
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
     * Approve a Comment
     * 
     * @return bool
     */
    public function approve()
    {
        $this->status = 'approved';
        $this->approved_at = date("Y-m-d H:i:s");
        return $this->save();
    }
    
    /**
     * Reject a Comment
     * 
     * @return bool
     */
    public function reject()
    {
        $this->status = 'rejected';
        $this->rejected_at = date("Y-m-d H:i:s");
        return $this->save();
    }

}
