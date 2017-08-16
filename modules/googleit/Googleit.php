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

    public function onConnect()
    {
        $this->getClient()->sendMessage('GoogleIt Plugin activated!');
    }
    
    /**
     * @param \TwitchBot\Message $message
     */
    public function onMessage($message)
    {
        if(substr($message->getMessage(), 0, 7) == '!google'){

            $userToPing = explode(' ', $message->getMessage())[1];

            $request = substr($message->getMessage(), 9 + strlen($userToPing));
            $url = "http://www.letmegooglethat.com/?q=" . urlencode($request);

            $message = sprintf($this->getConfig('message'), $userToPing, $url);

            $this->getClient()->sendMessage($message);
        }
    }
}
