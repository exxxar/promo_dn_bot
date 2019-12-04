<?php
/**
 * Created by PhpStorm.
 * User: exxxa
 * Date: 03.12.2019
 * Time: 21:06
 */

namespace App\Drivers;

use BotMan\BotMan\Drivers\HttpDriver;
use BotMan\BotMan\Interfaces\DriverInterface;
use BotMan\BotMan\Interfaces\UserInterface;
use BotMan\BotMan\Interfaces\WebAccess;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Users\User;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Illuminate\Support\Collection;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramInlineQueryDriver extends HttpDriver
{

    protected $payload;
    /** @var Collection */
    protected $event;
    const DRIVER_NAME = 'TelegramInlineQuery';

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest()
    {
        return !is_null($this->payload->get('inline_query'));
    }

    public function getName()
    {
        return self::DRIVER_NAME;
    }
    /**
     * Retrieve the chat message(s).
     *
     * @return array
     */
    public function getMessages()
    {
        return [new IncomingMessage( $this->event->get('query'), $this->event->get('from')['id'], $this->event->get('id'), $this->event)];
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        return !is_null(env("TELEGRAM_TOKEN"));
    }

    /**
     * Retrieve User information.
     * @param IncomingMessage $matchingMessage
     * @return UserInterface
     */
    public function getUser(IncomingMessage $matchingMessage)
    {
        return new User($matchingMessage->getSender());

    }

    /**
     * @param IncomingMessage $message
     * @return \BotMan\BotMan\Messages\Incoming\Answer
     */
    public function getConversationAnswer(IncomingMessage $message)
    {
        return Answer::create($message->getMessage());
    }

    /**
     * @param string|\BotMan\BotMan\Messages\Outgoing\Question $message
     * @param IncomingMessage $matchingMessage
     * @param array $additionalParameters
     * @return $this
     */
    public function buildServicePayload($message, $matchingMessage, $additionalParameters = [])
    {

        if (! $message instanceof WebAccess && ! $message instanceof OutgoingMessage) {
            $this->errorMessage = 'Unsupported message type.';
            $this->replyStatusCode = 500;
        }
        return [
            'message' => $message,
            'additionalParameters' => $additionalParameters,
        ];
    }


    /**
     * @param mixed $payload
     * @return Response
     */
    public function sendPayload($payload)
    {
        $response = $this->http->post($this->endpoint . 'send', [], $payload, [
            "Authorization: Bearer {$this->config->get('token')}",
            'Content-Type: application/json',
            'Accept: application/json',
        ], true);

        return $response;


    }

    public function getEvent(){
        return $this->event;
    }
    /**
     * @param Request $request
     * @return void
     */
    public function buildPayload(Request $request)
    {
        $this->payload = new ParameterBag((array)json_decode($request->getContent(), true));
        $this->event = Collection::make($this->payload->get('inline_query'));

    }

    /**
     * Low-level method to perform driver specific API requests.
     *
     * @param string $endpoint
     * @param array $parameters
     * @param \BotMan\BotMan\Messages\Incoming\IncomingMessage $matchingMessage
     * @return void
     */
    public function sendRequest($endpoint, array $parameters, IncomingMessage $matchingMessage)
    {
       $this->http->post('https://api.telegram.org/bot' . env("TELEGRAM_TOKEN") . '/'.$endpoint, [], $parameters);
    }

    public function reply($messages, $matchingMessage, $additionalParameters = [])
    {
        $parameters = array_merge([
            'cache_time' => 0,
            'inline_query_id' =>json_decode($this->event)->id,
        ], $additionalParameters);
        $results = [];
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        foreach ($messages as $message) {
            $result = [
                'type' => 'article',
                'id' => uniqid()
            ];
            /*
             * Questions are not possible in combination with
             * Telegram inline queries.
             */
            if ($message instanceof Question) {
                return false;
            } elseif ($message instanceof IncomingMessage) {
                $result['title'] = $message->getMessage();
                $result['input_message_content'] = [
                    'message_text' => $message->getMessage()
                ];
                if (!is_null($message->getImage())) {
                    $result['thumb_url'] = $message->getImage();
                }
            } elseif (is_array($message)) {
                $result = $message;
            }
            $results[] = $result;
        }
        $parameters['results'] = json_encode($results);
        return $this->http->post('https://api.telegram.org/bot' . env("TELEGRAM_TOKEN") . '/answerInlineQuery', [], $parameters);
    }
}