<?php

/**
 * Class Broadcast
 */
class Broadcast
{

    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    private $messages;

    private $currentQuestionNumber;

    /**
     * Broadcast constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);

        $this->messages = json_decode(file_get_contents(__DIR__ . '/messages.json'), true);

        $this->currentQuestionNumber = 0;
    }

    public function onConnect()
    {
        $this->getClient()->sendMessage('Broadcast Plugin activated !');
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
        $message = $this->getMessages($this->getCurrentQuestionNumber());

        if ($this->getCurrentQuestionNumber() < $this->getNumberOfMessages() - 1) {
            $this->setCurrentQuestionNumber($this->getCurrentQuestionNumber() + 1);
        } else {
            $this->setCurrentQuestionNumber(0);
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
     * @return int
     */
    private function getNumberOfMessages()
    {
        return count($this->getMessages());
    }

    /**
     * @param $value
     * @return $this
     */
    private function setCurrentQuestionNumber($value){
        $this->currentQuestionNumber = $value;

        return $this;
    }

    /**
     * @return int
     */
    private function getCurrentQuestionNumber(){
        return $this->currentQuestionNumber;
    }

}
