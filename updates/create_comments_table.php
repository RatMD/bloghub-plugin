<?php namespace RatMD\BlogHub\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use System\Classes\PluginManager;

/**
 * CreateCommentsTable Migration
 */
class CreateCommentsTable extends Migration
{

    /**
     * @inheritDoc
     */
    public function up()
    {
        if (!PluginManager::instance()->hasPlugin('RainLab.Blog')) {
            return;
        }

        Schema::create('ratmd_bloghub_comments', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('post_id')->unsigned();
            $table->string('status', 32)->default('pending');
            $table->string('title', 128)->default('');
            $table->text('content');
            $table->text('content_html');
            $table->boolean('favorite')->unsigned()->default(false);
            $table->integer('likes')->unsigned()->default(0);
            $table->integer('dislikes')->unsigned()->default(0);
            $table->string('author')->nullable();
            $table->string('author_email')->nullable();
            $table->string('author_uid')->nullable();
            $table->integer('parent_id')->unsigned()->nullable();
            $table->integer('author_id')->unsigned()->nullable();
            $table->integer('author_table')->unsigned()->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            
            $table->foreign('post_id')->references('id')->on('rainlab_blog_posts')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('ratmd_bloghub_comments')->onDelete('cascade');
        });

        Schema::table('rainlab_blog_posts', function (Blueprint $table) {
            $table->string('ratmd_bloghub_comment_mode', 32)->default('open');
            $table->boolean('ratmd_bloghub_comment_visible')->default(true);
        });
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        Schema::dropIfExists('ratmd_bloghub_comments');

        if (method_exists(Schema::class, 'dropColumns')) {
            Schema::dropColumns('rainlab_blog_posts', ['ratmd_bloghub_comment_mode', 'ratmd_bloghub_comment_visible']);
        } else {
            Schema::table('rainlab_blog_posts', function (Blueprint $table) {
                if (Schema::hasColumn('rainlab_blog_posts', 'ratmd_bloghub_comment_mode')) {
                    $table->dropColumn('ratmd_bloghub_comment_mode');
                }
            });
            Schema::table('rainlab_blog_posts', function (Blueprint $table) {
                if (Schema::hasColumn('rainlab_blog_posts', 'ratmd_bloghub_comment_visible')) {
                    $table->dropColumn('ratmd_bloghub_comment_visible');
                }
            });
        }
    }

}
