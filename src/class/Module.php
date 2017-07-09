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

    private $config;

    private $configFile;

    /**
     * Module constructor.
     * @param array $infos
     * @param $client
     */
    function __construct(array $infos, $client)
    {
        $this->client = $client;
        $this->infos = $infos;

        $reflection = new \ReflectionClass(__CLASS__);
        $moduleDir = dirname($reflection->getFileName());

        $this->configFile = $configFile = $moduleDir . '/config.json';

        if(file_exists($configFile)){
            $this->config = json_decode(file_get_contents($configFile), true);
        };
    }

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
     * Is call when twitch send PING command to the bot and bot reply PONG !
     * (Every 5 minutes)
     */
    public function onPong()
    {
    }

    /**
     * Is call when twitch send Usernotice (ex: sub)
     *
     * @param string $rawMsg
     */
    public function onUsernotice($rawMsg)
    {
    }

    /**
     * @return String
     */
    private function getInfo($info)
    {
        return (key_exists($info, $this->infos)) ? $this->infos[$info] : false;
    }

    /**
     * @return IrcConnect
     */
    private function getClient()
    {
        return $this->client;
    }

    /**
     * @return mixed
     */
    private function getConfig($key)
    {
        return $this->config[$key];
    }

    /**
     * @return mixed
     */
    private function setConfig($key, $value)
    {
        $config = $this->config;

        foreach ($config as $keyConfig => $v) {
            if ($keyConfig == $key) {
                $this->config[$keyConfig] = $value;
            }
        }

        file_put_contents($this->configFile, json_encode($this->config));
    }

}