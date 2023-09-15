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
        Schema::create('pets_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pet_id');
            $table->boolean('user_fullname_visible')->default(true);
            $table->boolean('user_location_visible')->default(true);
            $table->boolean('user_phone_visible')->default(true);
            $table->boolean('user_email_visible')->default(true);

            $table->foreign('pet_id')->references('id')->on('pets');

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
        Schema::dropIfExists('pets_settings');
    }
};
