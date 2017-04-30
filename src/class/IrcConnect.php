<?php

namespace TwitchBot;

/**
 * Class IrcConnect
 * @package TwitchBot
 */
class IrcConnect
{

    public static $RETURN = "\r\n";

    private $socket;

    private $address;

    private $port;

    private $user;

    private $password;

    private $nickname;

    private $channel;

    private $moduleLoader;


    /**
     * IrcConnect constructor.
     * @param String $address
     * @param Int $port
     * @param String $user
     * @param String $channel
     * @param String null $password
     */
    public function __construct($address, $port, $user, $channel, $password = null)
    {
        $this->address = $address;
        $this->port = $port;
        $this->user = $user;
        $this->channel = $channel;
        $this->password = $password;

        $this->moduleLoader = new ModuleLoader([
            "address" => $this->getAddress(),
            "port" => $this->getPort(),
            "user" => $this->getUser(),
            "channel" => $this->getChannel()
        ], $this);
    }

    /**
     * @return $this->socket;
     */
    public function connect(){
        $this->socket = fsockopen($this->getAddress(), $this->getPort());

        if(!$this->socket){
            new \Exception('Error when etablished connection to IRC');
        }

        //$this->sendRaw('CAP REQ :twitch.tv/tags'.self::$RETURN);
        //$this->sendRaw('CAP REQ :twitch.tv/commands'.self::$RETURN);
        $this->sendRaw('PASS ' . $this->getPassword().self::$RETURN);
        $this->sendRaw('NICK ' . $this->getUser().self::$RETURN);
        $this->sendRaw('JOIN #' . $this->getChannel().' '.self::$RETURN);

        $this->getModuleLoader()->hookAction('Connect');
        $this->sendToLog('Hook onConnect send');

        return $this->socket;
    }

    /**
     * @param String $raw
     */
    public function sendRaw($raw){
        fputs($this->socket, $raw.self::$RETURN);
    }

    /**
     * @param $socket
     */
    public function launch($socket){
        $connected = true;

        while ($connected){
            $data = fgets($socket);

            //$moderator = substr(strstr(strstr($data, 'user-type='), ' ', true), 10);

            $return = explode(':',$data);

            if(rtrim($return[0]) == 'PING'){
                $this->sendRaw('PONG :tmi.twitch.tv');
                $this->sendToLog('Ping Send !');
            }

            if($data){

                $this->sendToLog($data);

                if(preg_match('#:(.+):End Of /MOTD Command.#i',$data)){
                    $connected = false;
                    $this->sendToLog('End of connection, server send the end message', 'error');
                } else if (preg_match('/^:tmi.twitch.tv/',$data)){

                } else if (preg_match('/PRIVMSG/', $data)){
                    $message = $this->sanitizeMsg($data);

                    if($message->getUsername() != $this->getUser()){
                        $this->sendToLog('Hook onMessage send');
                        $this->getModuleLoader()->hookAction('Message', $message);
                    }
                }
            }
        }
    }

    /**
     * @param String $msg
     */
    public function sendMessage($msg){
        $this->sendRaw('PRIVMSG #'.$this->getChannel().' :'.$msg);
    }

    /**
     * @param String $msg
     * @param string $type
     */
    public function sendToLog($msg, $type = 'info'){

        switch ($type){
            case 'error':
                $toLog = "[ ERROR ] " . $msg;
                break;
            case 'info':
                $toLog = "[ INFO ] " . $msg;
                break;
            default:
                $toLog = "[ UNKNOW ] " . $msg;
        }

        echo $toLog . PHP_EOL;
    }

    /**
     * @param $rawMsg
     * @return Message
     */
    public function sanitizeMsg($rawMsg){
        preg_match('/:(.*?)\!/s', $rawMsg, $userR);
        $username = $userR[1];
        $message = strstr($rawMsg, 'PRIVMSG #' . $this->getChannel() .' :');
        $message = substr($message, 11 + strlen($this->getChannel()));

        $mesage = new Message(strtolower($username), $message, 0);

        return $mesage;
    }

    /**
     * @return String
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return Int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return String
     */
    public function getUser()
    {
        return strtolower($this->user);
    }

    /**
     * @return String
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return String
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * @return mixed
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * @return String
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return ModuleLoader
     */
    public function getModuleLoader(){
        return $this->moduleLoader;
    }

}