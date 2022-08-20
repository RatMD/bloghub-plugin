<?php declare(strict_types=1);

namespace RatMD\BlogHub\Updates;

use Schema;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use System\Classes\PluginManager;

/**
 * UpdateBackendUsers Migration
 */
class UpdateBackendUsersTable extends Migration
{

    /**
     * @inheritDoc
     */
    public function up()
    {
        if (!PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            return;
        }

        Schema::table('backend_users', function (Blueprint $table) {
            $table->string('display_name', 128)->nullable();
            $table->string('author_slug', 128)->unique()->nullable();
        });
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        Schema::dropColumns('backend_users', ['display_name', 'author_slug']);
    }

}
