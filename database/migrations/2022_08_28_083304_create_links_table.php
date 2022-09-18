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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('user_id');
            $table->string('page_id');
            $table->string('title')->nullable();
            $table->string('url')->nullable();
            $table->string('shorturl')->nullable();
            $table->integer('clicks')->default(0);
            $table->string('thumbnail')->nullable();
            $table->string('icon')->nullable();
            $table->string('position');
            $table->string('status');
            $table->softDeletes();
            $table->timestamps();
        });
        // title - url - clicks - page_id - user_id - image - status - pos
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('links');
    }
};
