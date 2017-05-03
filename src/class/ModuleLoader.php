<?php

namespace TwitchBot;

/**
 * Class ModuleLoader
 * @package TwitchBot
 */
class ModuleLoader
{
    private $modulesList;

    private $infos;

    private $client;

    /**
     * ModuleLoader constructor.
     */
    public function __construct($infos, $client)
    {
        $this->modulesList = $this->getList();

        $this->infos = $infos;
        $this->client = $client;
    }

    /**
     * @param $hook
     * @param null $data
     *
     * Hook available: Connect, Message, Ping, Pong, Usernotice
     *
     */
    public function hookAction($hook, $data = null)
    {
        foreach ($this->getModulesList() as $module) {
            /** @var Module $instance */
            $module = ucfirst($module);
            $instance = new $module($this->getInfos(), $this->getClient());
            $method = 'on' . $hook;
            $instance->$method($data);
        }
    }

    /**
     * @return array
     */
    private function getList()
    {
        $list = scandir(dirname(dirname(__DIR__)) . '/modules/');
        $list = array_diff($list, array('.', '..'));

        return $list;
    }

    /**
     * @return array
     */
    public function getModulesList()
    {
        return $this->modulesList;
    }

    /**
     * @return array
     */
    public function getInfos()
    {
        return $this->infos;
    }

    /**
     * @return IrcConnect mixed
     */
    public function getClient()
    {
        return $this->client;
    }

}