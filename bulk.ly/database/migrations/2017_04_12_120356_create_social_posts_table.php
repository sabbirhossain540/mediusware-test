<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned();
            $table->foreign('group_id')->references('id')->on('social_post_groups')->onDelete('cascade');
            $table->text('text')->nullable();
            $table->text('link')->nullable();
            $table->string('image')->nullable();
            $table->string('rsslink')->nullable();
            $table->tinyInteger('status')->default('0');
            $table->timestamp('schedule_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->text('hash')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('social_posts');
    }
}
