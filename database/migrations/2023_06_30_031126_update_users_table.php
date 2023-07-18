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
            $table->string('first_name', 50)->nullable()->comment('họ');
            $table->string('last_name', 50)->nullable()->comment('tên');
            $table->string('code', 50)->unique()->nullable();
            $table->tinyInteger('gender')->nullable()->comment('hiển thị thông tin người nhân cv');
            $table->string('job_title', 255)->nullable()->comment('link sub');
            $table->tinyInteger('current_rank')->nullable()->comment('cấp bậc hiện tại');
            $table->string('experience', 2)->nullable()->comment('năm kinh nghiệm');
            $table->tinyInteger('highest_degree')->nullable()->comment('bằng cấp cao nhất');
            $table->string('number_phone', 15)->nullable()->comment('số điện thoại');
            $table->date('birthday')->nullable()->comment('ngày sinh');
            $table->tinyInteger('nation')->nullable()->comment('quốc gia');
            $table->tinyInteger('marital_status')->nullable()->comment('tình trạng hôn nhân');
            
            $table->unsignedBigInteger('province_id')->nullable();
            $table->foreign('province_id')->references('id')->on('provinces');
            
            $table->unsignedBigInteger('district_id')->nullable();
            $table->foreign('district_id')->references('id')->on('districts');

            $table->string('address', 255)->nullable()->comment('địa chỉ');
            $table->tinyInteger('status')->nullable()->comment('trạng thái user');
            $table->string('avatar', 255)->nullable()->comment('hình đại diện');
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
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('status');
            $table->dropColumn('code');
            $table->dropColumn('gender');
            $table->dropColumn('job_title');
            $table->dropColumn('current_rank');
            $table->dropColumn('experience');
            $table->dropColumn('highest_degree');
            $table->dropColumn('number_phone');
            $table->dropColumn('birthday');
            $table->dropColumn('nation');
            $table->dropColumn('marital_status');
            $table->dropForeign(['province_id']);
            $table->dropColumn('province_id');
            $table->dropForeign(['district_id']);
            $table->dropColumn('district_id');
            $table->dropColumn('address');
        });
    }
};
