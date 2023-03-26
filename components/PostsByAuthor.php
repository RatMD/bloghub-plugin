<?php declare(strict_types=1);

namespace RatMD\BlogHub\Components;

use Backend\Models\User as BackendUser;
use RainLab\Blog\Components\Posts;
use RainLab\Blog\Models\Post;

class PostsByAuthor extends Posts
{

    /**
     * The post list filtered by this author model.
     *
     * @var ?BackendUser
     */
    public $author = null;

    /**
     * Declare Component Details
     *
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'          => 'ratmd.bloghub::lang.components.author.label',
            'description'   => 'ratmd.bloghub::lang.components.author.comment'
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
        $properties['authorFilter'] = [
            'title'         => 'ratmd.bloghub::lang.components.author.filter',
            'description'   => 'ratmd.bloghub::lang.components.author.filter_comment',
            'type'          => 'string',
            'default'       => '{{ :slug }}',
            'group'         => 'ratmd.bloghub::lang.components.bloghub_group',
        ];
        return $properties;
    }

    /**
     * Get BlogHub Layout Configuration
     *
     * @return array
     */
    protected function getBlogHubConfig()
    {
        $config = $this->controller->getLayout()->getComponent('bloghubBase');
        if ($config) {
            $config = $config->getProperties();
        }

        return array_merge([
            'archiveAuthor' => 'blog/author',
            'archiveDate' => 'blog/date',
            'archiveTag' => 'blog/tag',
            'authorUseSlugOnly' =>'0',
            'date404OnInvalid' => '1',
            'date404OnEmpty' => '1',
            'tagAllowMultiple' => '0',
        ], $config ?? []);
    }

    /**
     * Run Component
     * 
     * @return mixed
     */
    public function onRun()
    {
        $this->author = $this->page['author'] = $this->loadAuthor();

        if (empty($this->author)) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }

        return parent::onRun();
    }

    /**
     * List Posts
     *
     * @return mixed
     */
    protected function listPosts()
    {
        $author = $this->author->id;
        $category = $this->category ? $this->category->id : null;
        $categorySlug = $this->category ? $this->category->slug : null;

        /*
         * List all the posts, eager load their categories
         */
        $isPublished = !parent::checkEditor();

        $posts = Post::with(['categories', 'featured_images', 'ratmd_bloghub_tags', 'ratmd_bloghub_meta'])
            ->where('user_id', '=', $author)
            ->listFrontEnd([
                'page'             => $this->property('pageNumber'),
                'sort'             => $this->property('sortOrder'),
                'perPage'          => $this->property('postsPerPage'),
                'search'           => trim(input('search') ?? ''),
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
     * Load Author.
     *
     * @return BackendUser|null
     */
    protected function loadAuthor()
    {
        if (!$slug = $this->property('authorFilter')) {
            return null;
        }

        if(($user = BackendUser::where('ratmd_bloghub_author_slug', $slug)->first()) === null) {
            if ($this->getBlogHubConfig()['authorUseSlugOnly'] === '0') {
                if (($user = BackendUser::where('login', $slug)->first()) === null) {
                    return null;
                }

                if (!empty($user->ratmd_bloghub_author_slug)) {
                    return null;
                }
            } else {
                return null;
            }
        }

        return $user;
    }

}
