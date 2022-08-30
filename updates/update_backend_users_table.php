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
        Schema::table('backend_users', function (Blueprint $table) {
            $table->string('ratmd_bloghub_display_name', 128)->nullable();
            $table->string('ratmd_bloghub_author_slug', 128)->unique()->nullable();
        });
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        if (method_exists(Schema::class, 'dropColumns')) {
            Schema::dropColumns('backend_users', ['ratmd_bloghub_display_name', 'ratmd_bloghub_author_slug']);
        } else {
            Schema::table('backend_users', function (Blueprint $table) {
                if (Schema::hasColumn('backend_users', 'ratmd_bloghub_display_name')) {
                    $table->dropColumn('ratmd_bloghub_display_name');
                }
            });
            Schema::table('backend_users', function (Blueprint $table) {
                if (Schema::hasColumn('backend_users', 'ratmd_bloghub_author_slug')) {
                    $table->dropColumn('ratmd_bloghub_author_slug');
                }
            });
        }
    }

}
