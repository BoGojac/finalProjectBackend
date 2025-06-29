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
        Schema::create('voters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('polling_station_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->enum('gender', ['Male', 'Female']); // Changed to match frontend
            $table->date('registration_date');
            $table->date('birth_date');
            $table->enum('disability', ['None', 'Visual', 'Hearing', 'Physical', 'Intellectual', 'Other'])->default('None');
            $table->string('disability_type')->nullable();
            $table->string('duration_of_residence');
            $table->string('home_number')->nullable();
            $table->enum('voting_status', ['pending', 'voted'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voters');
    }
};
