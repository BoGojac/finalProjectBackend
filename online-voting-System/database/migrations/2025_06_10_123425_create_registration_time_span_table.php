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
            $table->enum('type', ['voter', 'candidate']);
            $table->date('beginning_date');
            $table->date('ending_date');
            $table->timestamps();

            $table->unique(['voting_date_id', 'type']); // ✅ enforce only one voter/candidate per voting date
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
