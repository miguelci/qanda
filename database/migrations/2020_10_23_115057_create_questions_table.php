<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('questions', static function (Blueprint $table): void {
            $table->increments('id');
            $table->string('question');
            $table->string('answer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
}
