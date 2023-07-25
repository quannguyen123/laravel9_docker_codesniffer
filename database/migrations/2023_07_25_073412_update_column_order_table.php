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
        Schema::table('orders', function (Blueprint $table) {
            $table->date('payment_date')->nullable()->after('payment_status')->comment('ngày thanh toán');
            $table->string('payment_transaction', 30)->nullable()->after('payment_date')->comment('mã thanh toán');
            $table->string('payment_response_code', 2)->nullable()->after('payment_transaction')->comment('trạng thái thanh toán trả về');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_date');
            $table->dropColumn('payment_transaction');
            $table->dropColumn('payment_response_code');
        });
    }
};
