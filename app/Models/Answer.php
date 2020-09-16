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

    public function save_by_request($request)
    {
        if ($request->answer === null OR $request->question_id === null) {
            throw new WebhookException('The request is incorrect');
        }
        $this->answer = $request->answer;
        $this->question_id = $request->question_id;
        $this->save();
    }

    public static function get_next_question_id(Answer $answer = null): int
    {
        $next_question_id = null;
        if ($answer !== null) {
            $next_question_id = $answer->question->next_question_id;
        } else {
            $next_question_id = 1;
        }
    }
}
