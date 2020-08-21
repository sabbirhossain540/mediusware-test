<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('type')->nullable();
            $table->string('price')->nullable();
            $table->string('ppm')->nullable();
            $table->string('connucted_buf_account')->nullable();
            $table->string('save_content_upload_post')->nullable();
            $table->string('save_content_upload_group')->nullable();
            $table->string('save_content_curation_feeds')->nullable();
            $table->string('save_content_curation_group')->nullable();
            $table->string('save_rss_auto_feeds')->nullable();
            $table->string('save_rss_auto_group')->nullable();
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
        Schema::dropIfExists('plans');
    }
}
