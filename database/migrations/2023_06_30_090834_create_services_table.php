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
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->comment('Tên dịch vụ');
            $table->tinyInteger('type')->nullable()->comment('loại dịch vụ');
            $table->string('price', 20)->nullable()->comment('giá dịch vụ');
            $table->integer('used_time')->nullable()->comment('thời gian sử dụng tính theo ngày');
            $table->text('description')->nullable()->comment('mô tả dịch vụ');
            $table->text('content')->nullable()->comment('nội dung dịch vụ');
            $table->string('image', 255)->nullable()->comment('hình ảnh mô tả');
            $table->string('note', 255)->nullable()->comment('hình ảnh mô tả');
            $table->tinyInteger('status')->nullable()->comment('trạng thái dịch vụ');
            
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
        Schema::dropIfExists('services');
    }
};
