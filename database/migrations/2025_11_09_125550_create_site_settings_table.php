<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('about')->nullable();
            $table->text('information')->nullable();
            $table->string('wa')->nullable();
            $table->string('wa_order')->nullable();
            $table->json('slider')->nullable();
            $table->text('home_description')->nullable();
            $table->string('banner_sidebar')->nullable();
            $table->string('banner_home_top')->nullable();
            $table->string('banner_home_bottom')->nullable();
            $table->text('terms')->nullable();
            $table->text('client')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
