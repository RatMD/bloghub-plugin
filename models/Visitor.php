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
        'posts'
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
        
        if (!in_array($post, $this->posts)) {
            $posts[] = $post;
            $this->setAttribute('posts', $posts);
            return $this->save();
        } else {
            return true;
        }
    }

}
