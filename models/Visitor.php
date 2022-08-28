<?php declare(strict_types=1);

namespace RatMD\BlogHub\Models;

use Model;
use RainLab\Blog\Models\Post;

class Visitor extends Model
{

    /**
     * Table associated with this Model
     * 
     * @var string
     */
    public $table = 'ratmd_bloghub_visitors';

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
        'user'
    ];

    /**
     * JSONable Model attributes
     * 
     * @var array
     */
    protected $jsonable = [
        'posts',
        'likes',
        'dislikes',
    ];

    /**
     * Get Current Visitor
     *
     * @return Visitor
     */
    static public function currentUser()
    {
        $user_id = hash_hmac('sha1', $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT'] ?? strval(strtotime(date('Y-m-d') . ' 00:00:00')));
        return self::firstOrCreate([
            'user' => $user_id
        ]);
    }

    /**
     * Check if user has seen
     *
     * @param Post|int The desired Post model or Post id to check.
     * @return boolean
     */
    public function hasSeen($post)
    {
        if ($post instanceof Post) {
            $post = $post->id;
        }

        $posts = $this->getAttribute('posts');
        if (!is_array($posts)) {
            $posts = [];
        }

        return in_array($post, $posts);
    }

    /**
     * Mark a Post as Seen
     *
     * @param Post|int The desired Post model or Post id to check.
     * @return boolean
     */
    public function markAsSeen($post)
    {
        if ($post instanceof Post) {
            $post = $post->id;
        }

        $posts = $this->getAttribute('posts');
        if (!is_array($posts)) {
            $posts = [];
        }
        
        if (!in_array($post, $posts)) {
            $posts[] = $post;
            $this->setAttribute('posts', $posts);
            return $this->save();
        } else {
            return true;
        }
    }

    /**
     * Get Comment Vote Status
     *
     * @param Comment|int $comment
     * @return ?string
     */
    public function getCommentVote($comment)
    {
        if ($comment instanceof Comment) {
            $comment = $comment->id;
        }

        $likes = $this->getAttribute('likes');
        $dislikes = $this->getAttribute('dislikes');
        if (is_array($likes) && in_array($comment, $likes)) {
            return 'like';
        } else if (is_array($dislikes) && in_array($comment, $dislikes)) {
            return 'dislike';
        } else {
            return null;
        }
    }

    /**
     * Add Comment Like
     *
     * @param Comment|int $comment
     * @return bool
     */
    public function addCommentLike($comment)
    {
        if ($comment instanceof Comment) {
            $comment = $comment->id;
        }

        $likes = $this->getAttribute('likes');
        if (!is_array($likes)) {
            $likes = [];
        }
        
        if (!in_array($comment, $likes)) {
            $likes[] = $comment;
            $this->setAttribute('likes', $likes);
            return $this->save();
        } else {
            return true;
        }
    }

    /**
     * Remove Comment Like
     *
     * @param Comment|int $comment
     * @return bool
     */
    public function removeCommentLike($comment)
    {
        if ($comment instanceof Comment) {
            $comment = $comment->id;
        }

        $likes = $this->getAttribute('likes');
        if (!is_array($likes)) {
            $likes = [];
        }
        
        if (!in_array($comment, $likes)) {
            return true;
        } else {
            $this->setAttribute('likes', array_filter($likes, fn($val) => $val !== $comment));
            return $this->save();
        }
    }

    /**
     * Add Comment Dislike
     *
     * @param Comment|int $comment
     * @return bool
     */
    public function addCommentDislike($comment)
    {
        if ($comment instanceof Comment) {
            $comment = $comment->id;
        }

        $dislikes = $this->getAttribute('dislikes');
        if (!is_array($dislikes)) {
            $dislikes = [];
        }
        
        if (!in_array($comment, $dislikes)) {
            $dislikes[] = $comment;
            $this->setAttribute('dislikes', $dislikes);
            return $this->save();
        } else {
            return true;
        }
    }

    /**
     * Remove Comment Dislike
     *
     * @param Comment|int $comment
     * @return bool
     */
    public function removeCommentDislike($comment)
    {
        if ($comment instanceof Comment) {
            $comment = $comment->id;
        }

        $dislikes = $this->getAttribute('dislikes');
        if (!is_array($dislikes)) {
            $dislikes = [];
        }
        
        if (!in_array($comment, $dislikes)) {
            return true;
        } else {
            $this->setAttribute('dislikes', array_filter($dislikes, fn($val) => $val !== $comment));
            return $this->save();
        }
    }

}
