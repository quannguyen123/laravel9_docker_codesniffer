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
        Schema::create('job_occupation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('occupation_id');
            $table->foreign('occupation_id')->references('id')->on('occupations');
            $table->unsignedBigInteger('job_id');
            $table->foreign('job_id')->references('id')->on('jobs');
        });
    } 

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('job_occupation');
    }
};
