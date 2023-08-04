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
        Schema::table('users', function (Blueprint $table) {
            $table->string('position', 50)->nullable()->comment('chức danh')->after('job_title');
            $table->integer('phonecode')->nullable()->comment('mã vùng điện thoại');
            $table->integer('file_cv')->nullable()->comment('file cv upload');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('position');
            $table->dropColumn('phonecode');
            $table->dropColumn('file_cv');
        });
    }
};
