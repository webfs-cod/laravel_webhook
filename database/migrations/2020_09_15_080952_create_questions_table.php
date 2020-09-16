<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('question');
            $table->integer('next_question_id')->unsigned();
            $table->foreign('next_question_id')->references('id')->on('questions');
            $table->integer('survey_id')->unsigned();
            $table->foreign('survey_id')->references('id')->on('survey');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['survey_id']);
            $table->dropForeign(['next_question_id']);
        });
        Schema::dropIfExists('questions');
    }
}
