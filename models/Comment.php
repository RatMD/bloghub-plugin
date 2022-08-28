<?php namespace RatMD\BlogHub\Models;

use Markdown;
use Model;

/**
 * Comment Model
 */
class Comment extends Model
{
    use \October\Rain\Database\Traits\NestedTree;
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
        'author' => 'string|min:3',
        'author_email' => 'email',
        'status' => 'required|in:pending,published,spam',
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
        'published_at'
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
     * Before Save Event Listener
     *
     * @return void
     */
    public function beforeSave()
    {
        if (empty($this->published_at)) {
            if ($this->status === 'published') {
                $this->published_at = date('Y-m-d H:i:s');
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
        $seconds = (time() - strtotime($this->published_at));

        if ($seconds >= 2419200) {
            return date('F, j. Y - H:i', strtotime($this->published_at));
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

}
