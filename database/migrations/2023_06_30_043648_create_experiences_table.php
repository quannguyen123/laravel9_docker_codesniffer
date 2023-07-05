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
        Schema::create('experiences', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('job_title', 255)->comment('Tên công việc');
            
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');
            
            $table->date('start_time', 10)->nullable()->comment('thời gian bắt đầu');
            $table->date('end_time', 10)->nullable()->comment('thời gian kết thúc');
            
            $table->tinyInteger('current_job')->nullable()->comment('công việc hiện tại');
            $table->text('description')->nullable()->comment('mô tả công việc');
            
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
        Schema::dropIfExists('experiences');
    }
};
