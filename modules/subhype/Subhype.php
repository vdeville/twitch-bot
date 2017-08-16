<?php


/**
 * Class Subhype
 */
class Subhype
{
    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    /**
     * Subhype constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);
    }

    public function onConnect()
    {
        $this->getClient()->sendMessage('SubHype Plugin activated !');
    }
    
    /**
     * @param \TwitchBot\Message $message
     */
    public function onUsernotice($message)
    {
        $rawMsg = $message->getOriginalMsg();
        $emojis = '';
        foreach ($this->getConfig('emotes') as $emoji) {
            $emojis .= ' ' . $emoji;
        }

        $isSample = strstr($rawMsg, '@twitchnotify.tmi.twitch.tv PRIVMSG #' . $this->getClient()->getChannel() . ' :');

        if ($isSample != false) {
            // SAMPLE OLD SCHOOL NOTIFY
            $message = substr($rawMsg, strlen($this->getClient()->getChannel()) + 39);
            $username = strstr($message, ' ', true);
            $message = sprintf($this->getConfig('message_sub'), $username, $emojis);
            $this->getClient()->sendMessage($message);
        } else {
            $username = strstr($rawMsg, 'login=');
            $username = strstr($username, ';', true);
            $username = str_replace('login=', '', $username);

            $months = strstr($rawMsg, 'msg-param-months=');
            if ($months != false) {
                $months = strstr($months, ';', true);
                $months = str_replace('msg-param-months=', '', $months);
                if ($months == '1') {
                    $months = false;
                }
            }

            if ($months != false) {
                $message = sprintf($this->getConfig('message_resub'), $username, $months, $emojis);
                $this->getClient()->sendMessage($message);
            } else {
                $message = sprintf($this->getConfig('message_new_sub'), $username, $emojis);
                $this->getClient()->sendMessage($message);
            }
        }

    }
    
}
