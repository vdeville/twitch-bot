<?php

/**
 * Class Commands
 */
class Commands {

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
        $this->getClient()->sendMessage('Plugin commands activate !');
    }

    /**
     * @param \TwitchBot\Message $data
     */
    public function onMessage($data)
    {
        if($data->getMessage()[0] == '!'){

            $command = trim($data->getMessage());
            $command = substr($command, 1);
            $explode = explode(' ',$command);
            $command = $explode[0];
            $userToPing = (isset($explode[1])) ? $explode[1] : false;

            if(key_exists($command, $this->getCommands())){
                $this->sendResponse($command, $userToPing);
            } else{
                $this->getClient()->sendToLog('The command !' . $command . ' is not mapped');
            }

        }
    }

    /**
     * @param null $key
     * @return array|string
     */
    public function getCommands($key = null)
    {
        if($key){
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
            if ($this->getCommands($command)[0] == '!') {
                return substr($this->getCommands($command), 1);
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
    public function sendResponse($command, $userToPing){

        $command = $this->getRealCommand($command);

        if(isset($this->lastCommands[$command])){
            $time = $this->lastCommands[$command];
        } else {
            $time = time() - $this->delay;
        }

        $diff = time() - $time;

        if($diff >= $this->delay){

            if($userToPing != false){
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