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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('sum_budget_recruitment', 50)->nullable()->comment('tổng ngân sách tuyển dụng');
            $table->tinyInteger('purpose')->nullable()->comment('Mục đích: đăng tuyển hay tìm CV');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('sum_budget_recruitment');
            $table->dropColumn('purpose');
        });
    }
};
