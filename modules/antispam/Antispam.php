<?php

/**
 * Class Antispam
 */
class Antispam
{

    use \TwitchBot\Module;

    private $config;

    /**
     * Antispam constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
        $this->client = $client;
        $this->infos = $infos;
    }

    public function onConnect()
    {
        $this->getClient()->sendMessage('Anti spam system is working on !');
    }

    /**
     * @param \TwitchBot\Message $data
     */
    public function onMessage($data)
    {
        /**
         * 0 = viewer
         * 1 = sub
         * 2 = mod
         * 3 = broadcaster
         */

        $message = strtolower($data->getMessage());

        if ($data->getUserType() < 2) {
            /** viewer & sub */

            $isBlacklisted = $this->isBlacklist($message);
            if ($isBlacklisted != false) {
                $this->timeout($data->getUsername(), 20);
                $this->getClient()->sendMessage($data->getUsername() . ' banni pour un mot ' . $isBlacklisted);
            }

            if ($this->asLink($message)) {
                $this->timeout($data->getUsername(), 20);
                $this->getClient()->sendMessage($data->getUsername() . ' timeout pour un lien');
            }

            if ($this->isTooLong($message)) {
                $this->timeout($data->getUsername(), 20);
                $this->getClient()->sendMessage($data->getUsername() . ' timeout, message trop long');
            }

            if ($this->tooManyCaps($data->getMessage())) {
                $this->timeout($data->getUsername(), 20);
                $this->getClient()->sendMessage($data->getUsername() . ' timeout, too many caps');
            }

        }


    }

    /**
     * @return mixed
     */
    private function getConfig($type)
    {
        return $this->config[$type];
    }

    /**
     * @param $message
     * @return bool
     */
    private function isBlacklist($message)
    {
        foreach ($this->getConfig('blacklisted_word') as $level => $words) {
            foreach ($words as $word) {
                if (preg_match('/' . $word . '/', $message)) {
                    return $level;
                } else {
                }
            }
        }
        return false;
    }

    /**
     * @param $message
     * @return bool
     */
    private function asLink($message)
    {
        $regex = "#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si";
        if (preg_match($regex, $message, $matches)) {
            if (preg_match('#http#', $matches[0])) {
                $domain = parse_url($matches[0], PHP_URL_HOST);
            } else {
                $domain = $matches[0];
            }
            return ($this->isAuthorizedDomain($domain)) ? false : true;
        } else {
            return false;
        }
    }

    /**
     * @param $url
     * @return bool
     */
    private function isAuthorizedDomain($url)
    {
        foreach ($this->getConfig('whitelist_domain') as $link) {
            if ($link == $url) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $message
     * @return bool
     */
    private function isTooLong($message)
    {
        if (strlen($message) > $this->getConfig('max_lenght')) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $message
     * @return bool
     */
    private function tooManyCaps($message){
        $capsCount = strlen(preg_replace('![^A-Z]+!', '', $message));
        $messageLenght = strlen($message);

        $pourcentCaps = $capsCount * 100 / $messageLenght;

        if($pourcentCaps > $this->getConfig('poucent_caps') AND $messageLenght > 8){
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param $user
     * @param $time
     */
    private function timeout($user, $time)
    {
        $this->getClient()->sendMessage('.timeout ' . $user . ' ' . $time);
        $this->getClient()->sendToLog('User ' . $user . ' tiemout ' . $time);
    }

}