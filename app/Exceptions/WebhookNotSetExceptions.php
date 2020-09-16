<?php


namespace App\Exceptions;
use Exception;
use Illuminate\Support\Facades\Log;


class WebhookNotSetExceptions extends WebhookException {
    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
        Log::debug('Webhook was not set');
    }
}
