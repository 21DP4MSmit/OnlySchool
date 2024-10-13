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
        Schema::create('absences', function (Blueprint $table) {
            $table->id('AbsenceID');
            $table->foreignId('UserID')->constrained('users')->onDelete('cascade');
            // Specify the custom column for the foreign key
            $table->foreignId('SubjectID')->constrained('subjects', 'SubjectID')->onDelete('cascade'); 
            $table->integer('Absence');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absences');
    }
};
