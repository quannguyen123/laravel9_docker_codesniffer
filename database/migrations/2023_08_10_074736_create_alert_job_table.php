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
        Schema::create('alert_jobs', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('position', 50)->nullable()->comment('chức danh');
            $table->string('salary_min', 20)->nullable()->comment('mức lương tối thiểu');
            $table->json('rank')->nullable()->comment('cấp bậc');
            $table->json('province')->nullable()->comment('tỉnh');
            $table->json('occupation')->nullable()->comment('ngành nghề');
            $table->json('industry')->nullable()->comment('lĩnh vực');
            $table->tinyInteger('interval')->nullable()->comment('tần suất');
            $table->tinyInteger('notification_by')->nullable()->comment('thông báo qua đâu: email, ứng dụng');
            $table->tinyInteger('status')->nullable()->comment('trạng thái kích hoạt');
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
        Schema::dropIfExists('alert_jobs');
    }
};
