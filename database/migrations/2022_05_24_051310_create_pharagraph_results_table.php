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
        Schema::create('pharagraph_results', function (Blueprint $table) {
            $table->id();
            $table->string('paragraph_id');
            $table->string('book_id');
            $table->text('result')->nullable();
            $table->enum('status',['START','ENDED']);
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
        Schema::dropIfExists('pharagraph_results');
    }
};
