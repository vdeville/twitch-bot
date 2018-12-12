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
            $this->getClient()->sendMessage('Commands Plugin Activated !');
        }
    }

    /**
     * @param \TwitchBot\Command $command
     */
    public function onCommand($command)
    {
        if (key_exists($command->getCommand(), $this->getCommands())) {
            $this->sendResponse($command);
            $this->getClient()->sendToLog("Command " . $command . " was send");
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
     * @param \TwitchBot\Command $command
     * @param $userToPing
     */
    public function sendResponse(\TwitchBot\Command $command)
    {
        if (isset($this->lastCommands[$command->getCommand()])) {
            $time = $this->lastCommands[$command->getCommand()];
        } else {
            $time = time() - $this->delay;
        }

        $diff = time() - $time;

        // Check if user who request the command has bypass access
        $rolesAuthorizedInCommon = array_intersect($this->getConfig('no_delay_for'), $command->getMessage()->getRoles());

        if ($diff >= $this->delay
            OR count($rolesAuthorizedInCommon) > 0) {

            $userToPing = false;

            if (count($command->getArgs()) == 2) {
                $userToPing = $command->getArgs()[1];
            }

            if ($userToPing != false) {
                $message = sprintf($this->getConfig('message_replytouser'), $userToPing, $this->getCommands($command));
                $this->getClient()->sendMessage($message);
            } else {
                $message = sprintf($this->getConfig('message_reply'), $this->getCommands($command));
                $this->getClient()->sendMessage($message);
            }

            $this->lastCommands[$command->getCommand()] = time();
        }

    }

}
