<?php

namespace TwitchBot;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

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
        $list = [];
        $finder = new Finder();
        $dirModule = dirname(dirname(__DIR__)) . '/modules/';

        /** @var SplFileInfo $module */
        foreach ($finder->directories()->in($dirModule) as $module){
            $list[] = $module->getRelativePathname();
        }

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