<?php

namespace TwitchBot;

/**
 * Class Message
 * @package TwitchBot
 */
class Message
{

    private $username;

    private $message;

    private $user_type;

    private $date;

    private $originalMsg;

    /**
     * Message constructor.
     * @param $originalMsg
     * @param $username
     * @param $message
     * @param int $userType
     */
    public function __construct($originalMsg, $username, $message, $userType = 0)
    {
        $this->username = $username;
        $this->message = $message;
        $this->user_type = $userType;
        $this->originalMsg = $originalMsg;

        $this->date = new \DateTime();
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getOriginalMsg()
    {
        return $this->originalMsg;
    }

    /**
     * @return int
     */
    public function getUserType()
    {
        return $this->user_type;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

}