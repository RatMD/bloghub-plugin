<?php declare(strict_types=1);

namespace RatMD\BlogHub\Classes;

use Cms\Classes\Controller;
use Illuminate\Support\Collection;
use RainLab\Blog\Models\Post;

class BlogHubPost
{

    /**
     * Post Model
     *
     * @var Post
     */
    protected Post $model;

    /**
     * Post Meta Collection
     *
     * @var ?Collection
     */
    protected ?Collection $metaCollection;

    /**
     * Post Tag Collection
     *
     * @var ?Collection
     */
    protected ?Collection $tagCollection;

    /**
     * Post Promoted Tag Collection
     *
     * @var ?Collection
     */
    protected ?Collection $promotedTagCollection;

    /**
     * Create a new BlogPost
     *
     * @param Post $model
     */
    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    /**
     * Call Dynamic Property Method
     *
     * @param string $method
     * @param ?array $arguments
     * @return void
     */
    public function __call($method, $arguments = [])
    {
        $methodName = str_replace('_', '', 'get' . ucwords($method, '_'));

        if (method_exists($this, $methodName)) {
            return $this->{$methodName}(...$arguments);
        } else {
            return null;
        }
    }

    /**
     * Get current CMS Controller
     *
     * @return Controller|null
     */
    protected function getController(): ?Controller
    {
        return Controller::getController();
    }

    /**
     * Get Post Comments
     *
     * @return mixed
     */
    public function getComments()
    { 
        return $this->model->ratmd_bloghub_comments;
    }

    /**
     * Get Post Comments Count
     *
     * @return integer
     */
    public function getCommentsCount()
    { 
        return $this->model->ratmd_bloghub_comments->count();
    }

    /**
     * Get Post Meta Data
     *
     * @return mixed
     */
    public function getMeta()
    { 
        if (empty($this->metaCollection)) {
            $this->metaCollection = $this->model->ratmd_bloghub_meta
                ->keyBy('name')
                ->map(fn($item) => $item['value']);
        }
        return $this->metaCollection;
    }

    /**
     * Get Post Tags
     *
     * @return mixed
     */
    public function getTags()
    {
        if (empty($this->tagCollection)) {
            $this->tagCollection = $this->model->ratmd_bloghub_tags;

            if (($ctrl = $this->getController()) !== null) {
                $viewBag = $ctrl->getLayout()->getViewBag()->getProperties();
                if (isset($viewBag['bloghubTagPage'])) {
                    $this->tagCollection->each(
                        fn ($tag) => $tag->setUrl($viewBag['bloghubTagPage'], $ctrl)
                    );
                }
            }
        }
        return $this->tagCollection;
    }

    /**
     * Get Promoted Post Tags
     *
     * @return mixed
     */
    public function getPromotedTags()
    { 
        if (empty($this->promotedTagCollection)) {
            $this->promotedTagCollection = $this->model->ratmd_bloghub_tags->where('promote', '1');

            if (($ctrl = $this->getController()) !== null) {
                $viewBag = $ctrl->getLayout()->getViewBag()->getProperties();
                if (isset($viewBag['bloghubTagPage'])) {
                    $this->promotedTagCollection->each(
                        fn ($tag) => $tag->setUrl($viewBag['bloghubTagPage'], $ctrl)
                    );
                }
            }
        }
        return $this->promotedTagCollection;
    }

}
