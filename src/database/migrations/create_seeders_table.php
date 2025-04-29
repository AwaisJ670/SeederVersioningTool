<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seeders', function (Blueprint $table) {
            $table->id();
            $table->string('seeder')->unique();
            $table->string('module')->nullable(); // Optional: in case no module is used
            $table->unsignedInteger('batch')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seeders');
    }
};
