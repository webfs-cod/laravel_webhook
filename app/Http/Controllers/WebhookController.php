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

        $respondent_id = Auth::user()->id;

        if (!empty($input)) {

            $next_question_id = Answer::get_next_question_id($input->answer_id);

            return $webhook->sendMessage(['char_id' => $input->respondent_id, 'next_question_id' => $next_question_id]);
//          return redirect()->route('webhook-input')->with(['id' => $respondent_id, 'next_question_id' => $next_question_id]);
        } else {
            $next_question_id = Answer::get_next_question_id();

            return $webhook->sendMessage(['char_id' => $input->respondent_id, 'next_question_id' => $next_question_id]);
//          return redirect()->route('webhook-input')->with(['id' => $respondent_id, 'next_question_id' => $next_question_id]);
        }
    }
}
