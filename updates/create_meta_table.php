<?php declare(strict_types=1);

namespace RatMD\BlogHub\Updates;

use Schema;
use Illuminate\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use System\Classes\PluginManager;

/**
 * CreateMetaTable Migration
 */
class CreateMetaTable extends Migration
{

    /**
     * @inheritDoc
     */
    public function up()
    {
        if (!PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            return;
        }

        Schema::create('ratmd_bloghub_meta', function(Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('name', 64);
            $table->text('value')->nullable();
            $table->integer('metable_id')->unsigned();
            $table->string('metable_type', 64);
            $table->timestamps();

            $table->unique(['name', 'metable_id', 'metable_type']);
        });
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        Schema::dropIfExists('ratmd_bloghub_meta');
    }
    
}
