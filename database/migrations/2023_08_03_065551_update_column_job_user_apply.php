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
        Schema::table('job_user_apply', function (Blueprint $table) {
            $table->string('position', 50)->nullable()->comment('chức danh');
            $table->integer('number_phone')->nullable()->comment('mã vùng điện thoại');
            $table->string('file_cv', 255)->nullable()->comment('file cv upload');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('job_user_apply', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->dropColumn('number_phone');
            $table->dropColumn('file_cv');
            
        });
    }
};
