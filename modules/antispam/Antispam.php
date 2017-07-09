<?php

/**
 * Class Antispam
 */
class Antispam
{

    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    /**
     * Antispam constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);
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

        if ($data->getMessage()[0] == '!') {

            $command = trim($data->getMessage());
            $command = substr($command, 1);
            $command = explode(' ', $command)[0];

            $command = strtolower($command);

            switch ($command) {
                case 'permitlink':
                    ($data->getUserType() == 3) ? $this->addPermitPeopleLink($data) : false;
                    break;
                case 'permitlinkoff':
                    ($data->getUserType() == 3) ? $this->removePermitPeopleLink($data) : false;
                    break;
                default:
                    break;
            }

        }

        if ($data->getUserType() < 2) {
            /** viewer & sub */

            $isBlacklisted = $this->isBlacklist($message);
            if ($isBlacklisted != false) {
                $this->timeout($data->getUsername(), $this->getConfig('timeout_blacklistedword'));
                $this->getClient()->sendMessage($data->getUsername() . ' timeout for blacklisted word level: ' . $isBlacklisted);
            }

            if ($this->asLink($message) AND !$this->isAuthorizedPepopleLink($data->getUsername())) {
                $this->timeout($data->getUsername(), $this->getConfig('timeout_link'));
                $this->getClient()->sendMessage($data->getUsername() . ' timeout for link');
            }

            if ($this->isTooLong($message)) {
                $this->timeout($data->getUsername(), $this->getConfig('timeout_toolong'));
                $this->getClient()->sendMessage($data->getUsername() . ' timeout message too long');
            }

            if ($this->tooManyCaps($data->getMessage())) {
                $this->timeout($data->getUsername(), $this->getConfig('timeout_toomanycaps'));
                $this->getClient()->sendMessage($data->getUsername() . ' timeout too many caps');
            }

        }


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
    private function tooManyCaps($message)
    {
        $capsCount = strlen(preg_replace('![^A-Z]+!', '', $message));
        $messageLenght = strlen($message);

        $pourcentCaps = $capsCount * 100 / $messageLenght;

        if ($pourcentCaps > $this->getConfig('pourcent_caps') AND $messageLenght > 8) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param $user
     * @return bool
     */
    private function isAuthorizedPepopleLink($user)
    {
        $user = strtolower($user);
        $authorizedPeoples = $this->getConfig('authorized_people');

        foreach ($authorizedPeoples as $username) {
            if ($user == $username) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \TwitchBot\Message $data
     */
    private function addPermitPeopleLink($data)
    {

        $storage = $this->getConfig('authorized_people');
        $user = substr($data->getMessage(), 12);
        $user = strtolower($user);

        if (!in_array($user, $storage)) {
            $storage[] = $user;
            $this->setConfig('authorized_people', $storage);
            $this->getClient()->sendMessage($user . ', is now authorized to post link');
        } else {
            $this->getClient()->sendMessage($user . ', is already authorized to post link');
        }
    }

    /**
     * @param \TwitchBot\Message $data
     */
    private function removePermitPeopleLink($data)
    {
        $storage = $this->getConfig('authorized_people');
        $user = substr($data->getMessage(), 15);
        $user = strtolower($user);

        $key = array_search($user, $storage);
        if ($key !== false) {
            unset($storage[$key]);
            $this->setConfig('authorized_people', $storage);
            $this->getClient()->sendMessage($user . ', is removed from authorized people to post link');
        } else {
            $this->getClient()->sendMessage($user . ', is already not authorized to post link');
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