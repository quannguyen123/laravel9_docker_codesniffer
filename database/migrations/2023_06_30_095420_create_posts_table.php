<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 500)->comment('tiêu đề bài post');
            $table->string('slug', 500)->nullable()->comment('slug bài post');
            $table->text('summary')->nullable()->comment('tóm tắt bài post');
            $table->text('content')->nullable()->comment('nội dung bài post');
            $table->string('image', 255)->nullable()->comment('hình ảnh bài post');
            $table->unsignedInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('admins');
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->string('deleted_by', 20)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('posts');
    }
};
