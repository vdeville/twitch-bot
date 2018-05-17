<?php


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
        if ($command == "blame" AND $command->getMessage()->getUserType() > 2) {
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
