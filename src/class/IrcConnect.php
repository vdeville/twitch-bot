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

    private $oauth;

    private $channel;

    private $channelPrettyName;

    private $moduleLoader;

    private $config;


    /**
     * IrcConnect constructor.
     * @param array $config
     */
    public function __construct($config)
    {
        $twitchConfig = $config["twitch"];

        $this->address = $twitchConfig["irc_address"];
        $this->port = $twitchConfig["port"];
        $this->user = $twitchConfig["user"];
        $this->channel = $channel = $twitchConfig["channels"];
        $this->oauth = $twitchConfig["oauth"];

        $this->channelPrettyName = $channel;

        $this->config = $config = [
            "twitch" => [
                "address" => $this->getAddress(),
                "port" => $this->getPort(),
                "user" => $this->getUser(),
                "channel" => $this->getChannel(),
                "channelPrettyName" => $this->getChannel(true)
            ],
            "general" => $config["general"]
        ];

        $this->moduleLoader = new ModuleLoader($config, $this);
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
        $this->sendRaw('CAP REQ :twitch.tv/commands' . self::$RETURN);
        $this->sendRaw('PASS ' . $this->getOauth() . self::$RETURN);
        $this->sendRaw('NICK ' . $this->getUser() . self::$RETURN);
        $this->sendRaw('JOIN #' . $this->getChannel() . ' ' . self::$RETURN);

        $this->getModuleLoader()->hookAction('Connect');
        $this->sendToLog('Hook onConnect send');

        return $this->socket;
    }

    /**
     * @param string $raw
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

        $commandSymbol = $this->getConfig("command_prefix");
        $commandSymbolLength = strlen($commandSymbol);

        while ($connected) {
            $data = fgets($socket);

            $return = explode(':', $data);
            if (rtrim($return[0]) == 'PING') {
                $this->sendRaw('PONG :' . $return[1]);
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

                    if (substr($message->getMessage(), 0, $commandSymbolLength) == $commandSymbol) {
                        $command = $this->sanitizeCommand($data);
                        $this->sendToLog('Hook onCommand send !');
                        $this->getModuleLoader()->hookAction('Command', $command);
                    }

                    if ($message->getUsername() != $this->getUser()) {
                        $this->sendToLog('Hook onMessage send !');
                        $this->getModuleLoader()->hookAction('Message', $message);
                    }

                    if (strstr(strtolower($message->getMessage()), '@' . $this->getUser())) {
                        $this->sendToLog('Hook onPing send !');
                        $this->getModuleLoader()->hookAction('Ping', $message);
                    }

                } else if (preg_match('/USERNOTICE/', $data) OR
                    preg_match('/twitchnotify!twitchnotify@twitchnotify.tmi.twitch.tv PRIVMSG #' . $this->getChannel() . '/', $data)
                ) {
                    $this->sendToLog('Hook onUsernotice send !');
                    $this->getModuleLoader()->hookAction('Usernotice', $data);
                }
            }
        }
    }

    /**
     * @param string $msg
     */
    public function sendMessage($msg)
    {
        $this->sendRaw('PRIVMSG #' . $this->getChannel() . ' :' . $msg);
    }

    /**
     * @param string $msg
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

        echo date('[d/m/y G:i:s]') . $toLog . PHP_EOL;
    }

    /**
     * @param $rawMsg
     * @return Message
     */
    public function sanitizeMsg($rawMsg)
    {
        $username = strstr($rawMsg, 'display-name=');
        $username = strstr($username, ';', true);
        $username = str_replace('display-name=', '', $username);

        $message = strstr($rawMsg, 'PRIVMSG #' . $this->getChannel() . ' :');
        $message = substr($message, 11 + strlen($this->getChannel()));

        $isMod = strstr($rawMsg, 'mod=');
        $isMod = substr($isMod, 4, 1);
        $isMod = boolval($isMod);

        $isSub = strstr($rawMsg, 'subscriber=');
        $isSub = substr($isSub, 11, 1);
        $isSub = boolval($isSub);

        $isBroadcaster = ($this->getChannel() == $username) ? true : false;

        /**
         * 0 = viewer
         * 1 = sub
         * 2 = mod
         * 3 = broadcaster
         */

        if ($isBroadcaster) $userType = 3;
        elseif ($isMod) $userType = 2;
        elseif ($isSub) $userType = 1;
        else $userType = 0;

        $message = new Message($rawMsg, $this->removeReturns($username), $this->removeReturns($message), $userType);

        return $message;
    }

    /**
     * @param $rawMsg
     * @return Command
     */
    public function sanitizeCommand($rawMsg)
    {
        $sanitizedMessage = $this->sanitizeMsg($rawMsg);

        $symbol = $this->getConfig("command_prefix");

        $command = new Command($symbol, $sanitizedMessage);

        return $command;
    }

    /**
     * @param $key
     * @param null $array
     * @return string|false
     */
    public function getConfig($key, $array = null)
    {
        if (is_null($array)) {
            $array = $this->config;
        }

        // is in base array?
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        // check arrays contained in this array
        foreach ($array as $element) {
            if (is_array($element)) {
                if ($value = $this->getConfig($key, $element)) {
                    return $value;
                }
            }

        }

        return false;
    }

    /**
     * @return string
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
     * @param bool $pretty
     * @return string
     */
    public function getUser($pretty = false)
    {
        return ($pretty) ? $this->user : strtolower($this->user);
    }

    /**
     * @return string
     */
    public function getOauth()
    {
        return $this->oauth;
    }

    /**
     * @return mixed
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * @param bool $pretty
     * @return string
     */
    public function getChannel($pretty = false)
    {

        return ($pretty) ? $this->channel : strtolower($this->channel);
    }

    /**
     * @return ModuleLoader
     */
    public function getModuleLoader()
    {
        return $this->moduleLoader;
    }

    /**
     * @param $string
     * @return string
     */
    public function removeReturns($string)
    {
        return str_replace("\r\n", '', $string);
    }

}