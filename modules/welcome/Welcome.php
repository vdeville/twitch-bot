<?php

/**
 * Class Welcome
 */
class Welcome
{

    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    /**
     * Welcome constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);
    }

    public function onConnect()
    {
        $this->getClient()->sendMe("is a bot created by Valentin Deville, his source code is available here: https://github.com/MyTheValentinus/twitch-bot");
        $this->getClient()->sendMessage('Welcome everyone to ' . $this->getInfo('channel') . '\'s channel !');
    }
}
