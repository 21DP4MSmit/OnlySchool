<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHomeworkTopicToSubjectListsTable extends Migration
{
    public function up()
    {
        Schema::table('subject_lists', function (Blueprint $table) {
            $table->text('homework')->nullable(); // Add homework column
            $table->text('topic')->nullable();    // Add topic column
        });
    }

    public function down()
    {
        Schema::table('subject_lists', function (Blueprint $table) {
            $table->dropColumn('homework');
            $table->dropColumn('topic');
        });
    }
}