<?php

use TwitchBot\Utils;

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
                $this->timeout($data, $this->getConfig('timeout_delay'));
            }
        }
    }

    /**
     * @param \TwitchBot\Message $message
     * @param $time
     */
    private function timeout($message, $time)
    {
        if ($this->getConfig('delete_instead_of_timeout')) {
            $this->deleteMessage($message);
        } else {
            $user = $message->getUsername();
            $this->getClient()->sendMessage('.timeout ' . $user . ' ' . $time);
            $this->getClient()->sendToLog('User ' . $user . ' timeout ' . $time);

            $msg = sprintf($this->getConfig('message'), $message->getUsername());
            $this->getClient()->sendMessage($msg);
        }
    }

    /**
     * @param \TwitchBot\Message $message
     */
    private function deleteMessage($message)
    {
        $this->getClient()->sendMessage('.delete ' . $message->getId());
        $this->getClient()->sendToLog('Remove message from ' . $message->getUsername() . ' with id ' . $message->getId());
    }
}
