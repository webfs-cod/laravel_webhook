<?php

namespace App\Models;

use App\Exceptions\WebhookException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }

    public function saveByRequest($request)
    {
        if ($request->answer === null || $request->questionId === null) {
            throw new WebhookException('The request is incorrect');
        }
        $this->answer = $request->answer;
        $this->questionId = $request->questionId;
        $this->save();
    }

    public static function getNextQuestionId(Answer $answer = null): int
    {
        $nextQuestionId = null;
        if ($answer !== null) {
            $nextQuestionId = $answer->question->nextQuestionId;
        } else {
            $nextQuestionId = 1;
        }

        return $nextQuestionId;
    }
}
