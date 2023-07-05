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
        Schema::create('reference_persons', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->comment('Tên người tham khảo');
            $table->string('job_title', 100)->nullable()->comment('chức danh');
            $table->string('email', 50)->nullable()->comment('email người tham khảo');
            $table->string('number_phone', 15)->nullable()->comment('sdt người tham khảo');

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
        Schema::dropIfExists('reference_persons');
    }
};
