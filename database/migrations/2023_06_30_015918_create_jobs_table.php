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
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('job_title', 255)->comment('Tiêu đề job');
            $table->string('slug', 255)->comment('link sub');
            $table->tinyInteger('rank')->nullable()->comment('cấp bậc tuyển dụng');
            $table->tinyInteger('job_type')->nullable()->comment('loại công việc');
            $table->text('description')->nullable()->comment('mô tả job');
            $table->text('job_require')->nullable()->comment('yêu cầu job');
            $table->string('salary_min', 20)->nullable()->comment('mức lương tối thiểu');
            $table->string('salary_max', 20)->nullable()->comment('mức lương tối đa');
            $table->tinyInteger('show_salary')->nullable()->comment('checkbox hiển thị mức lương');
            $table->tinyInteger('introducing_letter')->nullable()->comment('thư giới thiệu');
            $table->tinyInteger('language_cv')->nullable()->comment('ngôn ngữ nộp cv');
            $table->string('recipients_of_cv', 50)->nullable()->comment('tên người nhận cv');
            $table->tinyInteger('show_recipients_of_cv')->nullable()->comment('hiển thị thông tin người nhân cv');
            $table->string('email_recipients_of_cv', 50)->nullable()->comment('email người nhận cv');
            $table->string('post_anonymously', 50)->nullable()->comment('đăng tin tuyển dụng ẩn danh');

            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('created_by', 20)->nullable();
            $table->string('updated_by', 20)->nullable();
            $table->string('deleted_by', 20)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('jobs');
    }
};
