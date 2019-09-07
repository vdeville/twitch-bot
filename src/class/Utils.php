<?php

namespace TwitchBot;

/**
 * Class Utils
 * @package TwitchBot
 */
class Utils
{
    /**
     * @param $toCheck
     * @param Message $message
     * @return bool
     */
    public static function hasRole($toCheck, Message $message)
    {
        return (false === array_search($toCheck, $message->getRoles())) ? false : true;
    }

    /**
     * @param array $toCheck
     * @param Message $message
     * @return bool
     */
    public static function hasOneOfRoles(array $toCheck, Message $message)
    {
        foreach ($toCheck as $role) {
            if (Utils::hasRole($role, $message)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Message $message
     * @return bool
     */
    public static function isViewer(Message $message)
    {
        return true;
    }
    
    /**
     * @param Message $message
     * @return bool
     */
    public static function hasTurbo(Message $message)
    {
        return (Utils::hasRole(Message::$TURBO_USER, $message)) ? true : false;
    }

    /**
     * @param Message $message
     * @return bool
     */
    public static function isSub(Message $message)
    {
        return (Utils::hasRole(Message::$ROLE_SUB, $message)) ? true : false;
    }

    /**
     * @param Message $message
     * @return bool
     */
    public static function isVip(Message $message)
    {
        return (Utils::hasRole(Message::$ROLE_VIP, $message)) ? true : false;
    }

    /**
     * @param Message $message
     * @return bool
     */
    public static function isMod(Message $message)
    {
        return (Utils::hasRole(Message::$ROLE_MOD, $message)) ? true : false;
    }

    /**
     * @param Message $message
     * @return bool
     */
    public static function isOwner(Message $message)
    {
        return (Utils::hasRole(Message::$ROLE_OWNER, $message)) ? true : false;
    }

    /**
     * @param Message $message
     * @return bool
     */
    public static function isMoreThanViewer(Message $message)
    {
        return (count($message->getRoles()) !== 0) ? true : false;
    }
}
