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
        Schema::create('partner_invite', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->tinyInteger('role')->nullable()->comment('vai trò của partner');
            $table->string('token', 100);
            $table->date('expiration_date')->nullable()->comment('ngày hết hạn');
            
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->string('created_by', 20)->nullable();
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
        Schema::dropIfExists('partner_invite');
    }
};
