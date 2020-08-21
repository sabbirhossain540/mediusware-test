<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialPostGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('social_post_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->string('type');
            $table->text('files_links')->nullable();
            $table->tinyInteger('status')->default('0');
            $table->text('target_acounts')->nullable();
            $table->tinyInteger('recycle')->default('0');
            $table->tinyInteger('shuffle')->default('0');
            $table->string('interval')->default('daily');
            $table->tinyInteger('frequency')->default('1');
            $table->timestamp('start_time')->nullable()->useCurrent();
            $table->timestamp('end_time')->nullable();
            $table->timestamp('next_schedule_time')->nullable();
            $table->string('interval_seconds')->nullable();
            $table->string('hash')->nullable();
            $table->string('utm')->nullable();
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
        Schema::dropIfExists('social_post_groups');
    }
}