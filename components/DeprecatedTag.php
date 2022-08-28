<?php declare(strict_types=1);

namespace RatMD\BlogHub\Components;

use Log;

class DeprecatedTag extends PostsByTag
{
    
    /**
     * Declare Component Details
     *
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'          => 'ratmd.bloghub::lang.components.deprecated.tags_label',
            'description'   => 'ratmd.bloghub::lang.components.deprecated.tags_comment'
        ];
    }

    /**
     * @inheritDoc
     */
    public function onRun()
    {
        Log::notice('The [bloghubTagArchive] component is deprecated, please use [bloghubPostsByTag] instead.');
        parent::onRun();
    }

}
