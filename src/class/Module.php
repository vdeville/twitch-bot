<?php

namespace TwitchBot;

/**
 * Trait Module
 * @package TwitchBot
 */
trait Module
{

    private $infos;

    /** @var IrcConnect */
    private $client;

    /**
     * Is call when bot is connected to channel
     */
    public function onConnect(){}

    /**
     * Is call when message is send to channel
     *
     * @param String $message
     */
    public function onMessage($message){}

    /**
     * Is call when the bot was ping (@bot)
     *
     * @param String $message
     */
    public function onPing($message){}

    /**
     * @return String
     */
    public function getInfo($info){
        return (key_exists($info, $this->infos)) ? $this->infos[$info] : false;
    }

    /**
     * @return IrcConnect
     */
    public function getClient(){
        return $this->client;
    }

}