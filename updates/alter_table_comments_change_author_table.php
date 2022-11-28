<?php namespace RatMD\BlogHub\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use System\Classes\PluginManager;

/**
 * AlterTableCommentsChangeAuthorTable Migration
 */
class AlterTableCommentsChangeAuthorTable extends Migration
{

    /**
     * @inheritDoc
     */
    public function up()
    {
        if (!PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            return;
        }

        Schema::table('ratmd_bloghub_comments', function (Blueprint $table) {
            $table->string('author_table', 255)->nullable()->change();
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
