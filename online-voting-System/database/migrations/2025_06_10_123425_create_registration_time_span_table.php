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
        Schema::create('registration_time_spans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voting_date_id')->constrained()->onDelete('cascade');
            $table->dateTime('beginning_date');
            $table->dateTime('ending_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_time_spam');
    }
};
