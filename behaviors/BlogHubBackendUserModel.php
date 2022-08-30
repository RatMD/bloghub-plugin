<?php declare(strict_types=1);

namespace RatMD\BlogHub\Behaviors;

use Backend\Models\User;
use October\Rain\Extension\ExtensionBase;
use RatMD\BlogHub\Classes\BlogHubBackendUser;

class BlogHubBackendUserModel extends ExtensionBase
{

    /**
     * Parent Post Model
     *
     * @var User
     */
    protected User $model;

    /**
     * BlogHub Post Model DataSet
     *
     * @var ?BlogHubBackendUser
     */
    protected ?BlogHubBackendUser $bloghubSet;

    /**
     * Constructor
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;

        // Deprecated Methods
        $model->addDynamicMethod('bloghub_display', fn () => $this->getBloghubAttribute()->display());
        $model->addDynamicMethod('bloghub_slug', fn () => $this->getBloghubAttribute()->slug());
    }

    /**
     * Get main BlogHub Space
     *
     * @return BlogHubBackendUser
     */
    public function getBloghubAttribute()
    {
        if (empty($this->bloghubSet)) {
            $this->bloghubSet = new BlogHubBackendUser($this->model);
        }
        return $this->bloghubSet;
    }

}
