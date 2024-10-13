<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klase', function (Blueprint $table) {
            $table->id('ClassID'); // ClassID is now the primary key
            $table->foreignId('UserID')->constrained('users')->onDelete('cascade');
            $table->string('Department');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klase');
    }
};
