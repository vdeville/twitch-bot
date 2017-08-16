<?php

namespace TwitchBot;

/**
 * Class Command
 * @package TwitchBot
 */
class Command
{
    private $command;

    private $args;

    private $commandAndArgsRaw;

    private $message;

    /**
     * Command constructor.
     * @param $commandSymbol
     * @param Message $message
     */
    public function __construct($commandSymbol, Message $message)
    {
        $commandSymbolLength = strlen($commandSymbol);

        $this->commandAndArgsRaw = substr($message->getMessage(), $commandSymbolLength);
        $this->args = $args = explode(" ", $this->commandAndArgsRaw);
        $this->command = $args[0];
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @return bool|string
     */
    public function getCommandAndArgsRaw()
    {
        return $this->commandAndArgsRaw;
    }

    /**
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->command;
    }

}