<?php

/**
 * Class Responder
 */
class Responder {

    use \TwitchBot\Module;

    /**
     * Responder constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->client = $client;
        $this->infos = $infos;
    }

    public function onConnect()
    {
        $this->getClient()->sendMessage('Plugin responder activate !');
    }

    /**
     * @param \TwitchBot\Message $message
     */
    public function onPing($message)
    {
        $this->getClient()->sendMessage('You ping me @' . $message->getUsername() . ' ?! What do you want ?');
    }
}