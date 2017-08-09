<?php

/**
 * Class Commands
 */
class Commands
{

    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    private $commands;

    private $delay;

    private $lastCommands;

    /**
     * Commands constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);

        $this->commands = json_decode(file_get_contents(__DIR__ . '/commands.json'), true);

        $this->delay = $this->getConfig('delay');
        $this->lastCommands = [];
    }

    public function onConnect()
    {
        if ($this->getInfo('connect_message')) {
            $this->getClient()->sendMessage('Plugin commands activate !');
        }
    }

    /**
     * @param \TwitchBot\Command $command
     */
    public function onCommand($command)
    {
        $commandName = $command->getCommand();
        $args = $command->getArgs();
        $userToPing = false;

        if (key_exists($commandName, $this->getCommands())) {
            if (count($args) == 2) {
                $userToPing = $args[1];
            }

            $this->sendResponse($commandName, $userToPing);
            $this->getClient()->sendToLog("Command $commandName was send");
        }
    }

    /**
     * @param null $key
     * @return array|string
     */
    public function getCommands($key = null)
    {
        if ($key) {
            return $this->commands[$key];
        } else {
            return $this->commands;
        }
    }

    /**
     * @param $command
     * @return null|string
     */
    public function getRealCommand($command)
    {
        if (key_exists($command, $this->getCommands())) {

            $commandSymbol = $this->getInfo('command_prefix');
            $commandSymbolLength = strlen($commandSymbol);

            if (substr($this->getCommands($command), 0, $commandSymbolLength) == $commandSymbol) {
                return substr($this->getCommands($command), $commandSymbolLength);
            } else {
                return $command;
            }
        } else {
            return null;
        }
    }

    /**
     * @param $command
     * @param $userToPing
     */
    public function sendResponse($command, $userToPing)
    {

        $command = $this->getRealCommand($command);

        if (isset($this->lastCommands[$command])) {
            $time = $this->lastCommands[$command];
        } else {
            $time = time() - $this->delay;
        }

        $diff = time() - $time;

        if ($diff >= $this->delay) {

            if ($userToPing != false) {
                $message = sprintf($this->getConfig('message_replytouser'), $userToPing, $this->getCommands($command));
                $this->getClient()->sendMessage($message);
            } else {
                $message = sprintf($this->getConfig('message_reply'), $this->getCommands($command));
                $this->getClient()->sendMessage($message);
            }

            $this->lastCommands[$command] = time();
        }

    }

}