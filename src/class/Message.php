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

    /**
     * Message constructor.
     * @param $username
     * @param $message
     * @param int $user_type
     */
    public function __construct($username, $message, $user_type = 0)
    {
        $this->username = $username;
        $this->message = $message;
        $this->user_type = $user_type;

        $this->date = new \DateTime();
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
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