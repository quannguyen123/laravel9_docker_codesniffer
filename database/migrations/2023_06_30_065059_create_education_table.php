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
        Schema::create('education', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('major', 100)->comment('Tên chuyên ngành');
            $table->string('university', 100)->comment('Tên trường học');
            $table->tinyInteger('degree')->nullable()->comment('bằng cấp');
            $table->date('start_time', 10)->nullable()->comment('thời gian bắt đầu');
            $table->date('end_time', 10)->nullable()->comment('thời gian kết thúc');
            
            $table->text('achievement')->nullable()->comment('thành tựu');
            
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
        Schema::dropIfExists('education');
    }
};
