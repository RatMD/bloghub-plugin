<?php namespace RatMD\BlogHub\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use System\Classes\PluginManager;

/**
 * AlterTableMetaChangeMetableId Migration
 */
class AlterTableMetaChangeMetableId extends Migration
{

    /**
     * @inheritDoc
     */
    public function up()
    {
        if (!PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            return;
        }

        Schema::table('ratmd_bloghub_meta', function (Blueprint $table) {
            $table->integer('metable_id')->nullable()->change();
        });
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        // ...
    }

}
