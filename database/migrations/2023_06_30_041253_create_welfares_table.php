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
        Schema::create('welfares', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 255)->nullable()->comment('tên phúc lợi');
            $table->string('icon', 255)->nullable()->comment('icon phúc lợi');

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
        Schema::dropIfExists('welfares');
    }
};
