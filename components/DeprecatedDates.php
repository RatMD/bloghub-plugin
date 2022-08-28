<?php declare(strict_types=1);

namespace RatMD\BlogHub\Components;

use Log;

class DeprecatedDates extends PostsByDate
{
    
    /**
     * Declare Component Details
     *
     * @return array
     */
    public function componentDetails()
    {
        return [
            'name'          => 'ratmd.bloghub::lang.components.deprecated.dates_label',
            'description'   => 'ratmd.bloghub::lang.components.deprecated.dates_comment'
        ];
    }

    /**
     * @inheritDoc
     */
    public function onRun()
    {
        Log::notice('The [bloghubDateArchive] component is deprecated, please use [bloghubPostsByDate] instead.');
        parent::onRun();
    }

}
