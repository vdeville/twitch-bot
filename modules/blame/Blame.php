<?php

use TwitchBot\Utils;

/**
 * Class Blame
 */
class Blame
{
    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    /**
     * Blame constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);
    }

    /**
     * @param \TwitchBot\Command $command
     * @return bool
     */
    public function onCommand($command)
    {
        $message = $command->getMessage();
        if ($command == "blame" AND (Utils::isOwner($message) OR Utils::isMod($message))) {
            $this->blame($command->getArgs()[1]);
        }

        return true;
    }

    /**
     * @param $username
     */
    public function blame($username)
    {
        $message = sprintf($this->getConfig('blame_message'), $username);
        $this->getClient()->sendMessage($message);
    }
}
