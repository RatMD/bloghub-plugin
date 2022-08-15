<?php declare(strict_types=1);

namespace RatMD\BlogHub\Components;

use Illuminate\Contracts\Database\Query\Builder;
use RainLab\Blog\Components\Posts;
use RainLab\Blog\Models\Post;
use RatMD\BlogHub\Models\Tag;

class Tags extends Posts
{

    /**
     * The post list filtered by this tag model
     *
     * @var Model
     */
    public $tag;

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
        $properties = parent::defineProperties();
        $properties['tagFilter'] = [
            'title'       => 'ratmd.bloghub::lang.components.tag_filter_title',
            'description' => 'ratmd.bloghub::lang.components.tag_filter_description',
            'type'        => 'string',
            'default'     => '',
        ];
        return $properties;
    }

    /**
     * Prepare Variables
     *
     * @return void
     */
    protected function prepareVars()
    {
        $this->tag = $this->page['tag'] = $this->loadTag();
    }

    /**
     * List Posts
     *
     * @return mixed
     */
    protected function listPosts()
    {
        $tag = $this->tag->id;
        $category = $this->category ? $this->category->id : null;
        $categorySlug = $this->category ? $this->category->slug : null;

        /*
         * List all the posts, eager load their categories
         */
        $isPublished = !parent::checkEditor();

        $posts = Post::with(['categories', 'featured_images', 'bloghub_tags'])
            ->whereHas('bloghub_tags', function(Builder $query) use ($tag) {
                return $query->where('ratmd_bloghub_tags.id', $tag);
            })
            ->listFrontEnd([
                'page'             => $this->property('pageNumber'),
                'sort'             => $this->property('sortOrder'),
                'perPage'          => $this->property('postsPerPage'),
                'search'           => trim(input('search') ?? ''),
                'bloghub_tags'     => $tag,
                'category'         => $category,
                'published'        => $isPublished,
                'exceptPost'       => is_array($this->property('exceptPost'))
                    ? $this->property('exceptPost')
                    : preg_split('/,\s*/', $this->property('exceptPost'), -1, PREG_SPLIT_NO_EMPTY),
                'exceptCategories' => is_array($this->property('exceptCategories'))
                    ? $this->property('exceptCategories')
                    : preg_split('/,\s*/', $this->property('exceptCategories'), -1, PREG_SPLIT_NO_EMPTY),
            ]);

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $posts->each(function($post) use ($categorySlug) {
            $post->setUrl($this->postPage, $this->controller, ['category' => $categorySlug]);

            $post->categories->each(function($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });

        return $posts;
    }
    
    /**
     * Load Tag
     *
     * @return Tag|null
     */
    protected function loadTag()
    {
        if (!$slug = $this->property('tagFilter')) {
            return null;
        }

        $tag = new Tag;

        $tag = $tag->isClassExtendedWith('RainLab.Translate.Behaviors.TranslatableModel')
            ? $tag->transWhere('slug', $slug)
            : $tag->where('slug', $slug);

        $tag = $tag->first();
        return $tag ?: null;
    }


}
