<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Services\Webhook\WebhookService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class WebhookController extends Controller
{
    public function input($id)
    {
        $webhook = new WebhookService(
            route('webhook-input'),
            Config::get('services.webhook.api_key')
        );

        $input = $webhook->getInput($id);

        $respondentId = Auth::user()->id;

        if (!empty($input)) {

            $nextQuestionId = Answer::getNextQuestionId($input->answerId);

            return $webhook->sendMessage(['char_id' => $respondentId, 'next_question_id' => $nextQuestionId]);
//          return redirect()->route('webhook-input')->with(['id' => $respondent_id, 'next_question_id' => $next_question_id]);
        } else {
            $nextQuestionId = Answer::getNextQuestionId();

            return $webhook->sendMessage(['char_id' => $respondentId, 'next_question_id' => $nextQuestionId]);
//          return redirect()->route('webhook-input')->with(['id' => $respondent_id, 'next_question_id' => $next_question_id]);
        }
    }
}
