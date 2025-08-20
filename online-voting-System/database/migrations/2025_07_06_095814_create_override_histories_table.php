<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('override_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('voting_date_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // override initiator

            $table->enum('override_level', ['entire', 'constituency', 'pollingstation']);
            $table->foreignId('constituency_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('polling_station_id')->nullable()->constrained()->onDelete('cascade');

            $table->timestamp('override_date')->useCurrent();

            $table->boolean('rollback_status')->default(false);
            $table->foreignId('rollback_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rollback_date')->nullable();
            $table->date('substitution_date')->nullable(); // voting will be held again on this date

            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('override_histories');
    }
};
