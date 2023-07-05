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
        Schema::create('company_welfare', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('job_id');
            $table->foreign('job_id')->references('id')->on('jobs');
            $table->unsignedBigInteger('welfare_id');
            $table->foreign('welfare_id')->references('id')->on('welfares');
            $table->text('content')->comment('nội dung phúc lợi');
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_welfare'); 
    }
};
