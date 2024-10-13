<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_lists', function (Blueprint $table) {
            $table->id('ListID');
            $table->foreignId('ClassID')->constrained('klase', 'ClassID')->onDelete('cascade'); // Explicitly reference ClassID
            $table->foreignId('SubjectID')->constrained('subjects', 'SubjectID')->onDelete('cascade'); // Explicitly reference SubjectID
            $table->foreignId('ClassroomID')->constrained('classrooms')->onDelete('cascade');
            $table->date('Date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_lists');
    }
};
