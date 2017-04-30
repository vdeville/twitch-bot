<?php


/**
 * Class Twitchapi
 */
class Twitchapi
{
    use \TwitchBot\Module;

    private $config;

    /**
     * Twitchapi constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);

        $this->client = $client;
        $this->infos = $infos;
    }

    public function getLivestreamStatus(){
        $liveStatus = $this->makeRequest('https://api.twitch.tv/kraken/streams/'.$this->getChannelId($this->getInfo('channel')));
        $liveStatus = json_decode($liveStatus);

        return ($liveStatus->stream == null) ? 'offline' : 'online';
    }

    private function getChannelId($pseudo){
        $channel = $this->makeRequest('https://api.twitch.tv/kraken/channels/'.$this->getInfo('channel'));
        $channel = json_decode($channel);

        return $channel->_id;
    }

    private function makeRequest($url){
        $fields = [
            'Client-ID: '.$this->getConfig('clientId')
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $fields);

        $return = curl_exec($ch);
        curl_close($ch);

        return $return;
    }

    /**
     * @return mixed
     */
    private function getConfig($type)
    {
        return $this->config[$type];
    }
}