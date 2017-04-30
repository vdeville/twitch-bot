<?php

/**
 * Class Broadcast
 */
class Broadcast
{

    use \TwitchBot\Module;

    private $messages;

    /**
     * Broadcast constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->messages = json_decode(file_get_contents(__DIR__ . '/messages.json'), true);
        $this->client = $client;
        $this->infos = $infos;

        if (is_null($this->getSession('questionId'))) {
            $this->setSession('questionId', 0);
        }
    }

    public function onConnect()
    {
        $this->getClient()->sendMessage('Plugin broadcast activate !');
    }

    public function onPong()
    {
        $twitchApi = new Twitchapi($this->infos, $this->getClient());
        $livestreamStatus = $twitchApi->getLivestreamStatus();

        if($livestreamStatus == 'online'){
            $this->getClient()->sendMessage($this->getNextMessage());
        }
    }

    /**
     * @return string
     */
    private function getNextMessage()
    {
        $message = $this->getMessages($this->getQuestionId());

        if ($this->getQuestionId() < $this->getNumberOfMessages() - 1) {
            $this->setQuestionId($this->getQuestionId() + 1);
        } else {
            $this->setQuestionId(0);
        }

        return $message;
    }

    /**
     * @return string|array
     */
    public function getMessages($key = null)
    {
        if (!is_null($key)) {
            return $this->messages['messages'][$key];
        } else {
            return $this->messages['messages'];
        }
    }

    /**
     * @param int $questionId
     *
     * @return Broadcast()
     */
    public function setQuestionId($questionId)
    {
        $this->setSession('questionId', $questionId);
        return $this;
    }

    /**
     * @return int
     */
    public function getQuestionId()
    {
        return $this->getSession('questionId');
    }

    /**
     * @return int
     */
    private function getNumberOfMessages()
    {
        return count($this->getMessages());
    }

    /**
     * @param $key
     * @param $value
     */
    private function setSession($key, $value)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    private function getSession($key)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION[$key];
    }

}