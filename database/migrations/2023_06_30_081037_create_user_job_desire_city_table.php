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
        Schema::create('user_job_desire_city', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_job_desire_id');
            $table->foreign('user_job_desire_id')->references('id')->on('user_job_desires');
            
            $table->unsignedBigInteger('province_id');
            $table->foreign('province_id')->references('id')->on('provinces');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_job_desire_city');
    }
};
