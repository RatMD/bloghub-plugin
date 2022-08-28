<?php declare(strict_types=1);

namespace RatMD\BlogHub\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use RatMD\BlogHub\Models\Tag as TagModel;

class Tags extends ComponentBase
{
    /**
     * A collection of tags to display
     *
     * @var Collection
     */
    public $tags;

    /**
     * @var string Reference to the page name for linking to categories.
     */
    public $tagPage;

    /**
     * Component Details
     *
     * @return void
     */
    public function componentDetails()
    {
        return [
            'name'          => 'ratmd.bloghub::lang.components.tags_title',
            'description'   => 'ratmd.bloghub::lang.components.tags_description'
        ];
    }

    /**
     * Component Properties
     *
     * @return void
     */
    public function defineProperties()
    {
        return [
            'tagPage' => [
                'title'       => 'ratmd.bloghub::lang.components.tags_page',
                'description' => 'ratmd.bloghub::lang.components.tags_page_description',
                'type'        => 'dropdown',
                'default'     => 'blog/tag',
                'group'       => 'rainlab.blog::lang.settings.group_links',
            ],
        ];
    }

    /**
     * Get Tag Page Option
     *
     * @return void
     */
    public function getTagPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    /**
     * Run
     *
     * @return void
     */
    public function onRun()
    {
        $this->tagPage = $this->page['tagPage'] = $this->property('tagPage');
        $this->tags = $this->page['tags'] = $this->loadTags();
    }

    /**
     * Load popular tags
     * 
     * @return mixed
     */
    protected function loadTags()
    {
        $tags = TagModel::withCount(['posts_count'])
            ->where('posts_count_count', '>', 0)
            ->orderBy('posts_count_count', 'desc')
            ->limit(5)
            ->get();
        
        $tags->each(fn ($tag) => $tag->setUrl($this->tagPage, $this->controller));
        return $tags;
    }

}
