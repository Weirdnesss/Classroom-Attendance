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
        Schema::create('session_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained()->cascadeOnDelete();            
            $table->string('label')->nullable();
            $table->time('time_in_start');
            $table->time('time_in_end');
            $table->time('late_start');
            $table->time('time_out_start');
            $table->time('time_out_end');
            $table->unsignedTinyInteger('grace_minutes')->default(0);
            $table->boolean('late_enabled')->default(true);
            $table->boolean('timeout_enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_periods');
    }
};
