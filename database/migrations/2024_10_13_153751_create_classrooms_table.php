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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ClassID'); // Foreign key to the klase table
            $table->foreign('ClassID')->references('ClassID')->on('klase')->onDelete('cascade'); // Ensure ClassID exists in klase
            $table->foreignId('UserID')->constrained('users')->onDelete('cascade');
            $table->string('Classroom');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
