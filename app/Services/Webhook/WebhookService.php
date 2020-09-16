<?php


namespace App\Services\Webhook;


use App\Exceptions\WebhookException;
use App\Exceptions\WebhookMethodNotFindException;
use App\Exceptions\WebhookNotSetExceptions;
use GuzzleHttp\Client;

/**
 * Class Request
 *
 * @method static ServerResponse getUpdates(array $data)
 * Use this method to receive incoming updates using long polling (wiki). An Array of Update objects is returned.
 * @method static ServerResponse setWebhook(array $data)
 * Use this method to specify a url and receive incoming updates via an outgoing webhook.
 * Whenever there is an update for the chat, we will send an HTTPS POST request to the specified url, containing a JSON-serialized Update.
 * In case of an unsuccessful request, we will give up after a reasonable amount of attempts. Returns true.
 * @method static ServerResponse deleteWebhook()
 * Use this method to remove webhook integration if you decide to switch back to getUpdates. Returns True on success. Requires no parameters.
 * @method static ServerResponse getMe()
 * A simple method for testing your auth token. Requires no parameters. Returns basic information about the chat in form of a User object.
 * @method static ServerResponse sendPhoto(array $data)
 * Use this method to send photos. On success, the sent Message is returned.
 * @method static ServerResponse sendAudio(array $data)
 * Use this method to send audio files, if you want clients to display them in the music player. Your audio must be in the .mp3 format.
 * On success, the sent Message is returned. Respondent can currently send audio files of up to 50 MB in size, this limit may be changed in the future.
 * @method static ServerResponse sendDocument(array $data)
 * Use this method to send general files. On success, the sent Message is returned.
 * Respondent can currently send files of any type of up to 50 MB in size, this limit may be changed in the future.
 * @method static ServerResponse sendVideo(array $data)
 * Use this method to send video files, clients support mp4 videos (other formats may be sent as Document).
 * On success, the sent Message is returned. Respondent can currently send video files of up to 50 MB in size, this limit may be changed in the future.
 * @method static ServerResponse sendVoice(array $data)
 * Use this method to send audio files, if you want clients to display the file as a playable voice message.
 * For this to work, your audio must be in an .ogg file encoded with OPUS (other formats may be sent as Audio or Document). On success, the sent Message is returned.
 * Respondent can currently send voice messages of up to 50 MB in size, this limit may be changed in the future.
 * @method static ServerResponse leaveChat(array $data)
 * Use this method for your leaving a group, supergroup or channel. Returns True on success.
 * @method static ServerResponse getChat(array $data)
 * Use this method to get up to date information about the chat (current name of the user for one-on-one conversations, current username of a user, group or channel, etc.).
 * Returns a Chat object on success.
 * @method static ServerResponse deleteMessage(array $data)
 * Use this method to delete a message, including service messages, with certain limitations. Returns True on success.
 */
class WebhookService
{
    private static $actions = [
        'getUpdates',
        'setWebhook',
        'deleteWebhook',
        'getMe',
        'sendMessage',
        'sendPhoto',
        'sendAudio',
        'sendDocument',
        'sendVideo',
        'sendVoice',
        'leaveChat',
        'getChat',
        'deleteMessage',
    ];
    private $client;
    private $apiKey;

    /**
     * Создаем HTTP (Guzzle) клиента
     * WebhookService constructor.
     * @param string $baseUri
     * @param string $apiKey
     */
    public function __construct(string $baseUri, string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->client = new Client([
            'base_uri' => $baseUri
        ]);
    }

    /**
     * Получаем данные с потока ввода
     * @return array|string
     * @throws WebhookException
     */
    public static function getInput(int $respondentId = null)
    {
        $input = file_get_contents('php://input');
        if (!is_string($input)) {
            throw new WebhookException('Input must be a string!');
        }
        $input = json_decode($input);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new WebhookException('Input must be converted to json!');
        }
        if ($respondentId !== null) {
            $inputByRespondentId = [];
            foreach ($input as $key => $val) {
                if ($val->respondentId === $respondentId) {
                    $inputByRespondentId[] = $val;
                }
            }
            $input = $inputByRespondentId;
        }

        return $input;
    }

    /**
     * Проверка на существование вебхук метода
     * @param string $action
     * @throws WebhookMethodNotFindException
     */
    private static function isValidAction($action)
    {
        if (!in_array($action, self::$actions, true)) {
            throw new WebhookMethodNotFindException('The action "' . $action . '" doesn\'t exist!');
        }
    }

    /**
     * Вызываем метод из списка actions
     * @param string $action
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $action, array $arguments)
    {
        /* Для проверки на наличие метода */
        array_unshift($data, $action);

        return call_user_func([$this, 'execute'], $arguments);
    }

    /**
     * @param $action
     * @param array $data
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function execute($action, array $data = [])
    {
        $result = null;
        $response = null;

        try {
            $response = $this->client->post(
                '/' . $this->apiKey . '/' . $action,
                $data
            );
            $result = (string)$response->getBody();
        } catch (WebhookNotSetExceptions $e) {
            $response = null;
            $result = $e->getResponse() ? (string)$e->getResponse()->getBody() : '';
        }

        return $result;
    }
}
