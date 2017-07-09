<?php


/**
 * Class Subhype
 */
class Subhype
{
    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    private $config;

    /**
     * Subhype constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);

        $this->config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
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
            $this->getClient()->sendMessage($username . ' has just subscribed !' . $emojis);
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
                $this->getClient()->sendMessage($username . ' has just resub from ' . $months . ' months !' . $emojis);
            } else {
                $this->getClient()->sendMessage($username . ' has just subscribed for the first time !' . $emojis);
            }
        }

    }

    public function onConnect()
    {
        $this->getClient()->sendMessage('Plugin subhype activate !');
    }

    /**
     * @return mixed
     */
    private function getConfig($type)
    {
        return $this->config[$type];
    }
}