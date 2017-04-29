<?php

/**
 * Class Welcome
 */
class Welcome {

    use \TwitchBot\Module;

    /**
     * Welcome constructor.
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
        $this->getClient()->sendMessage('Welcome every body to '.$this->getInfo('channel').' channel !');
    }
}