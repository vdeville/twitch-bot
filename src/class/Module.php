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
    public function onConnect()
    {
    }

    /**
     * Is call when message is send to channel
     *
     * @param Message $data
     */
    public function onMessage($data)
    {
    }

    /**
     * Is call when the bot was ping (@bot)
     *
     * @param Message $data
     */
    public function onPing($data)
    {
    }

    /**
     * @return String
     */
    public function getInfo($info)
    {
        return (key_exists($info, $this->infos)) ? $this->infos[$info] : false;
    }

    /**
     * @return IrcConnect
     */
    public function getClient()
    {
        return $this->client;
    }

}