<?php

/**
 * Class Googleit
 */
class Googleit
{
    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    /**
     * Googleit constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);
    }

    /**
     * @param \TwitchBot\Command $command
     */
    public function onCommand($command)
    {
        if($command == "google"){

            $args = $command->getArgs();
            if (count($args) > 2){
                $userToPing = $args[1];

                $request = substr($command->getMessage()->getMessage(), 9 + strlen($userToPing));
                $url = "http://www.letmegooglethat.com/?q=" . urlencode($request);

                $message = sprintf($this->getConfig('message'), $userToPing, $url);

                $this->getClient()->sendMessage($message);
            }

        }
    }
    public function onConnect()
    {
        if ($this->getInfo('connect_message')) {
            $this->getClient()->sendMessage('Plugin google\'it activate !');
        }
    }
}