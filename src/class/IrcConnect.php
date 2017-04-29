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
    }

    /**
     * @return $this->socket;
     */
    public function connect(){
        $this->socket = fsockopen($this->getAddress(), $this->getPort());

        if(!$this->socket){
            new \Exception('Error when etablished connection to IRC');
            return false;
        }

        //$this->sendRaw('CAP REQ :twitch.tv/tags'.self::$RETURN);
        //$this->sendRaw('CAP REQ :twitch.tv/commands'.self::$RETURN);
        $this->sendRaw('PASS ' . $this->getPassword().self::$RETURN);
        $this->sendRaw('NICK ' . $this->getUser().self::$RETURN);
        $this->sendRaw('JOIN ' . $this->getChannel().' '.self::$RETURN);

        $this->sendMessage('[ INFO ] $> Bot connected');

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
            $return = explode(':',$data);

            if(rtrim($return[0]) == 'PING'){
                $this->sendRaw('PONG :tmi.twitch.tv');
                $this->sendToLog('Ping Send !');
            }

            if($data){
                echo $data;

                if(preg_match('#:(.+):End Of /MOTD Command.#i',$data)){
                    $connected = false;
                    $this->sendToLog('End of connection, server send the end message', 'error');
                }
            }
        }
    }

    public function sendMessage($msg){
        $this->sendRaw('PRIVMSG #'.$this->getChannel().' :'.$msg);
    }

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
        return $this->user;
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

}