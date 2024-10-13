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
        Schema::create('marks', function (Blueprint $table) {
            $table->id('MarkID');
            $table->foreignId('UserID')->constrained('users')->onDelete('cascade');
            $table->foreignId('SubjectID')->constrained('subjects', 'SubjectID')->onDelete('cascade'); // Specify the referenced column
            $table->string('mark');
            $table->date('date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};

