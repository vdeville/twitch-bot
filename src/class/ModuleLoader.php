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

    private $config;

    private $client;

    private $modulesInstances;

    /**
     * ModuleLoader constructor.
     * @param array $config
     * @param IrcConnect $client
     */
    public function __construct($config, $client)
    {
        $this->config = $config;
        $this->client = $client;

        $this->modulesList = $this->getList();
        $this->modulesInstances = $this->instancesAllModules();
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
        foreach ($this->getInstancesModules() as $module) {
            $method = 'on' . $hook;
            $module->$method($data);
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
     * @return \stdClass[]
     */
    public function getInstancesModules(){
        return $this->modulesInstances;
    }

    /**
     * @return array
     */
    public function instancesAllModules(){
        $instances = [];
        foreach ($this->getModulesList() as $module){
            $module = ucfirst($module);
            $instances[$module] = new $module($this->getConfig(), $this->getClient());
        }

        return $instances;
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
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return IrcConnect mixed
     */
    public function getClient()
    {
        return $this->client;
    }

}