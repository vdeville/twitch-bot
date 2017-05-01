<?php

/**
 * Class Commands
 */
class Commands {

    use \TwitchBot\Module;

    private $commands;

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
            $command = explode(' ',$command)[0];

            if(key_exists($command, $this->getCommands())){
                $this->getClient()->sendMessage($data->getUsername() . ', ' . $this->getCommands($command));
            } else{
                $this->getClient()->sendToLog('The command !' . $command . ' is not mapped');
            }

        }
    }

    /**
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

}