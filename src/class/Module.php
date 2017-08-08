<?php

namespace TwitchBot;

/**
 * Trait Module
 * @package TwitchBot
 */
trait Module
{

    private $config;

    /** @var IrcConnect */
    private $client;

    private $configModule;

    private $configModuleFile;

    /**
     * Module constructor.
     * @param array $config
     * @param $client
     */
    function __construct(array $config, $client)
    {
        $this->client = $client;
        $this->config = $config;

        $reflection = new \ReflectionClass(__CLASS__);
        $moduleDir = dirname($reflection->getFileName());

        $this->configModuleFile = $configModuleFile = $moduleDir . '/config.json';

        if (file_exists($configModuleFile)) {
            $this->configModule = json_decode(file_get_contents($configModuleFile), true);
        }
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
     * @param $info
     * @param array|null $config
     * @return String
     */
    private function getInfo($info, $config = null)
    {
        if(is_null($config)){
            $config = $this->config;
        }

        // is in base array?
        if (array_key_exists($info, $config)) {
            return $config[$info];
        }

        // check arrays contained in this array
        foreach ($config as $element) {
            if (is_array($element)) {
                if ($value = $this->getInfo($info, $element)) {
                    return $value;
                }
            }

        }

        return false;
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
        return $this->configModule[$key];
    }

    /**
     * @return mixed
     */
    private function setConfig($key, $value)
    {
        $config = $this->configModule;

        foreach ($config as $keyConfig => $v) {
            if ($keyConfig == $key) {
                $this->configModule[$keyConfig] = $value;
            }
        }

        return file_put_contents($this->configModuleFile, json_encode($this->configModule, JSON_PRETTY_PRINT));
    }

}