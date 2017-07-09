<?php

/**
 * Class Meblock
 */
class Meblock {

    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    /**
     * Meblock constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);
    }

    /**
     * @param \TwitchBot\Message $data
     */
    public function onMessage($data)
    {
        if(preg_match("/ACTION/", $data->getOriginalMsg()) && $data->getUserType() < 2){
            $this->timeout($data->getUsername(), 20);
            $this->getClient()->sendMessage('@' . $data->getUsername() . ', usage of /me is not permit here.');
        }
    }

    /**
     * @param $user
     * @param $time
     */
    private function timeout($user, $time)
    {
        $this->getClient()->sendMessage('.timeout ' . $user . ' ' . $time);
        $this->getClient()->sendToLog('User ' . $user . ' tiemout ' . $time);
    }
}