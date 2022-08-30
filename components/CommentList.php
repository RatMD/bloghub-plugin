<?php declare(strict_types=1);

namespace RatMD\BlogHub\Components;

use Lang;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use RainLab\Blog\Models\Post;
use RatMD\BlogHub\Models\Comment;

class CommentList extends ComponentBase
{

    /**
     * Comments Collection
     *
     * @var mixed
     */
    protected $comments = [];

    /**
     * Declare Component Details
     *
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'        => 'ratmd.bloghub::lang.components.comments_list.label',
            'description' => 'ratmd.bloghub::lang.components.comments_list.comment'
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
            'postPage' => [
                'title'             => 'rainlab.blog::lang.settings.posts_post',
                'description'       => 'rainlab.blog::lang.settings.posts_post_description',
                'type'              => 'dropdown',
                'default'           => '',
                'group'             => 'rainlab.blog::lang.settings.group_links',
            ],
            'excludePosts' => [
                'title'             => 'ratmd.bloghub::lang.components.comments_list.exclude_posts',
                'description'       => 'ratmd.bloghub::lang.components.comments_list.exclude_posts_description',
                'type'              => 'string',
                'default'           => '',
            ],
            'amount' => [
                'title'             => 'ratmd.bloghub::lang.components.comments_list.amount',
                'description'       => 'ratmd.bloghub::lang.components.comments_list.amount_description',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'ratmd.bloghub::lang.components.comments_list.amount_validation',
                'default'           => '5',
            ],
            'sortOrder' => [
                'title'             => 'rainlab.blog::lang.settings.posts_order',
                'description'       => 'rainlab.blog::lang.settings.posts_order_description',
                'type'              => 'dropdown',
                'default'           => 'published_at desc',
            ],
            'onlyFavorites' => [
                'title'             => 'ratmd.bloghub::lang.components.comments_list.only_favorites',
                'description'       => 'ratmd.bloghub::lang.components.comments_list.only_favorites_description',
                'type'              => 'checkbox',
                'default'           => '0'
            ],
            'hideOnDislikes' => [
                'title'             => 'ratmd.bloghub::lang.components.comments_section.hide_on_dislike',
                'description'       => 'ratmd.bloghub::lang.components.comments_section.hide_on_dislike_description',
                'type'              => 'string',
                'default'           => '0'
            ]
        ];
    }

    /**
     * Get Post Page Dropdown Options
     *
     * @return void
     */
    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
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
     * Run Component
     *
     * @return mixed
     */
    public function onRun()
    {
        $this->comments = $this->page['comments'] = $this->listComments();
    }

    /**
     * Get Post Ids
     *
     * @return array
     */
    protected function getPostIds()
    {

        $ids = array_map('trim', explode(',', $this->property('excludePosts')));

        foreach ($ids AS &$id) {
            if (is_numeric($id)) {
                $id = intval($id);
            } else {
                if(($post = Post::where('slug', $id)->first()) !== null) {
                    $id = intval($post->id);
                } else {
                    $id = null;
                }
            }
        }

        return array_filter($ids);
    }

    /**
     * List Comments
     *
     * @return void
     */
    protected function listComments()
    {
        $query = Comment::with('post')->where('status', 'approved');

        // Show only Favourites
        if ($this->property('onlyFavorites') === '1') {
            $query->where('favorite', '1');
        }
        
        // Hide on Dislike
        if (($value = $this->property('hideOnDislikes')) !== '0') {
            if (strpos($value, ':') === 0 && is_numeric(substr($value, 1))) {
                $val = substr($value, 1);
                $query->whereRaw("(dislikes == 0 OR dislikes / likes < $val)");
            } else {
                $query->where('dislikes', '<', $value);
            }
        }

        // Exclude Posts
        $post_ids = $this->getPostIds();
        if (!empty($post_ids)) {
            $query->whereNotIn('post_id', $post_ids);
        }

        // Configure Sort Order
        $order = $this->property('sortOrder');
        if (!array_key_exists($order, $this->getSortOrderOptions())) {
            $order = 'created_at DESC';
        }
        $orders = explode(' ', $order);
        $query->orderBy($orders[0], strtoupper($orders[1]) === 'DESC'? 'DESC': 'ASC');

        // Configure Amount
        $limit = intval($this->property('amount'));
        $query->limit($limit);

        $postPage = empty($this->property('postPage')) ? 'blog/post' : $this->property('postPage');
        $ctrl = $this->controller;
        return $query->get()->each(function ($item) use ($postPage, $ctrl) {
            $item->post->setUrl($postPage, $ctrl);
        });
    }

}
