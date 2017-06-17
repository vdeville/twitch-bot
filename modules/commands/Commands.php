<?php

/**
 * Class Commands
 */
class Commands {

    use \TwitchBot\Module;

    private $commands;

    private static $DELAY = 30;

    private $lastCommands;

    /**
     * Commands constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->commands = json_decode(file_get_contents(__DIR__ . '/commands.json'), true);
        $this->client = $client;
        $this->infos = $infos;

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
     * @return mixed
     */
    public function getCommands($key = null)
    {
        if($key){
            $command = $this->commands[$key];
            if($command[0] == '!'){
                $key = substr($command, 1);
            }
            return $this->commands[$key];
        } else {
            return $this->commands;
        }
    }

    /**
     * @param $command
     * @param $userToPing
     */
    public function sendResponse($command, $userToPing){

        if(isset($this->lastCommands[$command])){
            $time = $this->lastCommands[$command];
        } else {
            $time = time() - self::$DELAY;
        }

        $diff = time() - $time;

        if($diff >= self::$DELAY){

            if($userToPing != false){
                $this->getClient()->sendMessage($userToPing . ', ' . $this->getCommands($command));
            } else {
                $this->getClient()->sendMessage($this->getCommands($command));
            }

            $this->lastCommands[$command] = time();
        }

    }

}