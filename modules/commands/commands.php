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
        $this->infos = $infos;
    }

    public function onConnect()
    {
        $this->getClient()->sendMessage('Plugin commands activate !');
    }

    /**
     * @param $message
     */
    public function onMessage($message)
    {
        $this->getClient()->sendMessage('Message receive!');
    }

}