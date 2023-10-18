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
        Schema::create('client_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_code_id')
                ->constrained('qr_codes');
            $table->foreignId('client_id')
                ->constrained('clients');
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
        Schema::dropIfExists('client_qr_codes');
    }
};
