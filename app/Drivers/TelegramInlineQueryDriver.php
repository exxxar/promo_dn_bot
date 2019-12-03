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
use BotMan\BotMan\Users\User;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Illuminate\Support\Collection;

class TelegramInlineQueryDriver extends HttpDriver
{

    /**
     * Determine if the request is for this driver.
     *
     * @return bool
     */
    public function matchesRequest()
    {
        // TODO: Implement matchesRequest() method.

        return !is_null($this->payload->get('inline_query')) && !is_null($this->payload->get('update_id'));
    }

    /**
     * Retrieve the chat message(s).
     *
     * @return array
     */
    public function getMessages()
    {
        // TODO: Implement getMessages() method.

        return [new IncomingMessage($this->event->get('query'), $this->event->get('from')['id'], $this->event->get('id'), $this->event)];
    }

    /**
     * @return bool
     */
    public function isConfigured()
    {
        // TODO: Implement isConfigured() method.
        return !is_null($this->config->get('telegram_token'));
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
        // TODO: Implement getConversationAnswer() method.
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
        // TODO: Implement buildServicePayload() method.
        //
        //
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
        // TODO: Implement sendPayload() method.

        $response = $this->http->post($this->endpoint . 'send', [], $payload, [
            "Authorization: Bearer {$this->config->get('token')}",
            'Content-Type: application/json',
            'Accept: application/json',
        ], true);

        return $response;


    }

    /**
     * @param Request $request
     * @return void
     */
    public function buildPayload(Request $request)
    {
        // TODO: Implement buildPayload() method.
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
        // TODO: Implement sendRequest() method.
    }
}