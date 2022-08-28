<?php declare(strict_types=1);

namespace RatMD\BlogHub\Components;

use Lang;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;

class CommentList extends ComponentBase
{

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
            'published_at DESC' => Lang::get('ratmd.bloghub::lang.sorting.published_at_desc'),
            'published_at ASC'  => Lang::get('ratmd.bloghub::lang.sorting.published_at_asc'),
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

    }

}
