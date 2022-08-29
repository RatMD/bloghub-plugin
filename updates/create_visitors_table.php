<?php declare(strict_types=1);

namespace RatMD\BlogHub\Updates;

use Schema;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use System\Classes\PluginManager;

/**
 * CreateVisitorsTable Migration
 */
class CreateVisitorsTable extends Migration
{

    /**
     * @inheritDoc
     */
    public function up()
    {
        if (!PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            return;
        }

        Schema::create('ratmd_bloghub_visitors', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('user', 64);
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
            $table->json('posts')->nullable();
=======
            $table->json('posts')->default('[]');
>>>>>>> bd5ef37 ([DEV])
=======
            $table->json('posts')->nullable();
>>>>>>> b5ca129 ([FIX] MySQL installation issue)
=======
            $table->json('posts')->nullable();
>>>>>>> cf1e26566d17acfbb97d62620c9f54cfeb237bfa

            $table->timestamps();
        });
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        Schema::dropIfExists('ratmd_bloghub_visitors');
    }

}
