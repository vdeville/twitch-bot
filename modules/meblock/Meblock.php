<?php

use TwitchBot\Utils;

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
        $timeout = true;

        if(preg_match("/ACTION/", $data->getOriginalMsg())){
            if($this->getConfig('allow_sub') AND Utils::isSub($data)) {
                $timeout = false;
            }

            if($this->getConfig('allow_vip') AND Utils::isVip($data)) {
                $timeout = false;
            }

            if(Utils::isMod($data) OR Utils::isOwner($data)) {
                $timeout = false;
            }

            if ($timeout) {
                $this->timeout($data->getUsername(), $this->getConfig('timeout_delay'));

                $message = sprintf($this->getConfig('message'), $data->getUsername());
                $this->getClient()->sendMessage($message);
            }
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
