<?php

/**
 * Class Commands
 */
class Commands {

    use \TwitchBot\Module;

    private $commands;

    /**
     * Commands constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->commands = file_get_contents(__DIR__ . '/commands.json');
        $this->client = $client;
    }

    public function onConnect()
    {
        $this->client->sendMessage('Plugin commands activate !');
    }

    /**
     * @param $message
     */
    public function onMessage($message)
    {
        $this->client->sendMessage('Message receive!');
    }

}