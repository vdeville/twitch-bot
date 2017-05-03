<?php


/**
 * Class Subhype
 */
class Subhype
{
    use \TwitchBot\Module;

    private $config;

    /**
     * Subhype constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);

        $this->client = $client;
        $this->infos = $infos;
    }

    /**
     * @param \TwitchBot\Message $rawMsg
     */
    public function onUsernotice($rawMsg)
    {
        $username = strstr('login=', $rawMsg);
        $username = strstr(';', $username, true);

        $emojis = '';

        foreach ($this->getConfig('emotes') as $emoji) {
            $emojis .= ' ' . $emoji;
        }

        $this->getClient()->sendMessage($username . ' has just subscribed !' . $emojis);
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