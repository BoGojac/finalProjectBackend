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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('party_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('constituency_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('gender', ['Male', 'Female']); // Changed to match frontend
            $table->date('registration_date');
            $table->date('birth_date');
            $table->enum('disability', ['None', 'Visual', 'Hearing', 'Physical', 'Intellectual', 'Other'])->default('None');
            $table->string('disability_type')->nullable();
            $table->string('duration_of_residence');
            $table->string('home_number')->nullable();
            $table->string('image')->nullable();
            $table->string('original_image_name')->nullable();
            $table->enum('candidate_type', ['individual', 'party'])->default('individual'); // Added
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
