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
    public function connect()
    {
        $this->socket = fsockopen($this->getAddress(), $this->getPort());

        if (!$this->socket) {
            new \Exception('Error when etablished connection to IRC');
        }

        $this->sendRaw('CAP REQ :twitch.tv/tags' . self::$RETURN);
        //$this->sendRaw('CAP REQ :twitch.tv/commands'.self::$RETURN);
        $this->sendRaw('PASS ' . $this->getPassword() . self::$RETURN);
        $this->sendRaw('NICK ' . $this->getUser() . self::$RETURN);
        $this->sendRaw('JOIN #' . $this->getChannel() . ' ' . self::$RETURN);

        $this->getModuleLoader()->hookAction('Connect');
        $this->sendToLog('Hook onConnect send');

        return $this->socket;
    }

    /**
     * @param String $raw
     */
    public function sendRaw($raw)
    {
        fputs($this->socket, $raw . self::$RETURN);
    }

    /**
     * @param $socket
     */
    public function launch($socket)
    {
        $connected = true;

        while ($connected) {
            $data = fgets($socket);

            $return = explode(':', $data);
            if (rtrim($return[0]) == 'PING') {
                $this->sendRaw('PONG :'.$return[1]);
                $this->sendToLog('Ping Send !');
                $this->getModuleLoader()->hookAction('Pong');
                $this->sendToLog('Hook onPong send !');
            }

            if ($data) {
                $this->sendToLog($data);

                if (preg_match('#:(.+):End Of /MOTD Command.#i', $data)) {
                    $connected = false;
                    $this->sendToLog('End of connection, server send the end message', 'error');
                } else if (preg_match('/^:tmi.twitch.tv/', $data)) {
                    // Information about connection
                } else if (preg_match('/PRIVMSG/', $data)) {
                    $message = $this->sanitizeMsg($data);

                    if ($message->getUsername() != $this->getUser()) {
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
    public function sendMessage($msg)
    {
        $this->sendRaw('PRIVMSG #' . $this->getChannel() . ' :' . $msg);
    }

    /**
     * @param String $msg
     * @param string $type
     */
    public function sendToLog($msg, $type = 'info')
    {
        switch ($type) {
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
    public function sanitizeMsg($rawMsg)
    {
        preg_match('/:(.*?)\!/s', $rawMsg, $userR);
        $username = $userR[1];
        $username = strtolower($username);

        $message = strstr($rawMsg, 'PRIVMSG #' . $this->getChannel() . ' :');
        $message = substr($message, 11 + strlen($this->getChannel()));

        $isMod = strstr($rawMsg, 'mod=');
        $isMod = substr($isMod, 4, 1);
        $isMod = boolval($isMod);

        $isSub = strstr($rawMsg, 'subscriber=');
        $isSub = substr($isSub, 11, 1);
        $isSub = boolval($isSub);

        $isBroacaster = ($this->getChannel() == $username) ? true : false;

        /**
         * 0 = viewer
         * 1 = sub
         * 2 = mod
         * 3 = broadcaster
         */

        if ($isBroacaster) $userType = 3;
        elseif ($isMod) $userType = 2;
        elseif ($isSub) $userType = 1;
        else $userType = 0;

        $message = new Message($rawMsg, $username, $message, $userType);

        return $message;
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
        return strtolower($this->channel);
    }

    /**
     * @return ModuleLoader
     */
    public function getModuleLoader()
    {
        return $this->moduleLoader;
    }

}