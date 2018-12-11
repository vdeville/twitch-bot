<?php

namespace TwitchBot;

/**
 * Class Message
 * @package TwitchBot
 */
class Message
{
    private $id;

    private $username;

    private $message;

    private $roles;

    private $date;

    private $originalMsg;

    public static $ROLE_SUB = 'ROLE_SUB';

    public static $ROLE_VIP = 'ROLE_VIP';

    public static $ROLE_MOD = 'ROLE_MODERATOR';

    public static $ROLE_OWNER = 'ROLE_OWNER';

    /**
     * Message constructor.
     * @param string $originalMsg
     * @param string $id
     * @param string $username
     * @param string $message
     * @param array $roles
     */
    public function __construct($originalMsg, $id, $username, $message, $roles = [])
    {
        $this->id = $id;
        $this->username = $username;
        $this->message = $message;
        $this->roles = $roles;
        $this->originalMsg = $originalMsg;

        $this->date = new \DateTime();
    }

    /**
     * @param bool $pretty
     * @return string
     */
    public function getUsername($pretty = false)
    {
        return ($pretty) ? $this->username : strtolower($this->username);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param string $toCheck
     * @return bool
     */
    public function hasRole($toCheck)
    {
        return (false === array_search($toCheck, $this->getRoles())) ? false : true;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

}