<?php declare(strict_types=1);

namespace RatMD\BlogHub\Updates;

use Schema;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use System\Classes\PluginManager;

/**
 * UpdateBackendUsers Migration
 */
class UpdateBackendUsersTableAgain extends Migration
{

    /**
     * @inheritDoc
     */
    public function up()
    {
        Schema::table('backend_users', function (Blueprint $table) {
            $table->text('ratmd_bloghub_about_me')->nullable();
        });
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        if (method_exists(Schema::class, 'dropColumns')) {
            Schema::dropColumns('backend_users', ['ratmd_bloghub_about_me']);
        } else {
            Schema::table('backend_users', function (Blueprint $table) {
                if (Schema::hasColumn('backend_users', 'ratmd_bloghub_about_me')) {
                    $table->dropColumn('ratmd_bloghub_about_me');
                }
            });
        }
    }

}
