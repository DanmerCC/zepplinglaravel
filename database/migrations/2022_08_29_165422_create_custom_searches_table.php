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
        Schema::create('custom_searches', function (Blueprint $table) {
            $table->id();
            $table->date('day');
            $table->string('hour');
            $table->string('pid')->nullable();
            $table->text('output')->nullable();
            $table->string('ip_publica');
            $table->enum('state', ['STARTED', 'FAILED', 'REDEADBLE', 'ENDED']);
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
        Schema::dropIfExists('custom_searches');
    }
};
