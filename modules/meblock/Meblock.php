<?php

/**
 * Class Meblock
 */
class Meblock {

    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    private $userLevel;

    /**
     * Meblock constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);
        $this->userLevel = ($this->getConfig('allow_sub')) ? 1 : 2;
    }

    public function onConnect()
    {
        if ($this->getInfo('connect_message')) {
            $this->getClient()->sendMessage('Meblock Plugin activated !');
        }
    }
    /**
     * @param \TwitchBot\Message $data
     */
    public function onMessage($data)
    {
        if(preg_match("/ACTION/", $data->getOriginalMsg()) && $data->getUserType() < $this->userLevel){
            $this->timeout($data->getUsername(), $this->getConfig('timeout_delay'));

            $message = sprintf($this->getConfig('message'), $data->getUsername());
            $this->getClient()->sendMessage($message);
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
