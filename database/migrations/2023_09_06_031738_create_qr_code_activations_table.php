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
        Schema::create('qr_code_activations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('qr_code_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('pet_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('qr_code_id')->references('id')->on('qr_codes');
            $table->foreign('user_id')->references('id')->on('users')->nullable();
            $table->foreign('pet_id')->references('id')->on('pets')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qr_code_activations');
    }
};
