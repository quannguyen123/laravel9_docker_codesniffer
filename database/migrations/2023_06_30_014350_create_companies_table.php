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
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->comment('tên công ty');
            $table->string('number_phone', 15)->nullable()->comment('só điện thoại công ty');
            $table->string('address', 255)->nullable()->comment('dịa chỉ công ty');
            $table->tinyInteger('size')->nullable()->comment('số người công ty selectbox');
            $table->string('recipients_of_cv', 255)->nullable()->comment('email người nhận cv');
            $table->text('info')->nullable()->comment('thông tin giới thiệu công ty');
            $table->string('logo', 255)->nullable()->comment('logo công ty');
            $table->string('banner', 255)->nullable()->comment('banner của công ty');
            $table->string('video', 255)->nullable()->comment('link video youtube');
            $table->tinyInteger('status')->nullable()->comment('trạng thái công ty');
            $table->softDeletes();
            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->string('deleted_by', 20)->nullable();
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
        Schema::dropIfExists('companies');
    }
};
