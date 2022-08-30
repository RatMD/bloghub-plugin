<?php declare(strict_types=1);

namespace RatMD\BlogHub\Models;

use October\Rain\Database\Model;

class MetaSettings extends Model
{

    /**
     * Implement Interfaces
     *
     * @var array
     */
    public $implement = ['System.Behaviors.SettingsModel'];

    /**
     * Settings Mode
     *
     * @var string
     */
    public $settingsCode = 'ratmd_bloghub_meta_settings';

    /**
     * Settings Fields
     *
     * @var string
     */
    public $settingsFields = 'fields.yaml';

}
