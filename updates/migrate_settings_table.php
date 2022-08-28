<?php declare(strict_types=1);

namespace RatMD\BlogHub\Updates;

use Illuminate\Support\Facades\DB;
use October\Rain\Database\Updates\Migration;

/**
 * MigrateSettingsTable Migration
 */
class MigrateSettingsTable extends Migration
{

    /**
     * @inheritDoc
     */
    public function up()
    {

        // Rename settings to meta settings
        DB::table('system_settings')
            ->where('item', 'ratmd_bloghub_settings')
            ->update([
                'item' => 'ratmd_bloghub_meta_settings'
            ]);
    }

    /**
     * @inheritDoc
     */
    public function down()
    {

        // Delete BlogHub core Settings
        DB::table('system_settings')
            ->where('item', 'ratmd_bloghub_core_settings')
            ->delete();
        
        // Rename meta settings back to settings 
        DB::table('system_settings')
            ->where('item', 'ratmd_bloghub_meta_settings')
            ->update([
                'item' => 'ratmd_bloghub_settings'
            ]);
    }

}
