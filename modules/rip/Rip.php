<?php

/**
 * Class Rip
 */
class Rip
{
    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    private static $COUNTERFILE = __DIR__ . '/counter.txt';

    private $lastUseAdd;

    private $lastUse;

    /**
     * Rip constructor.
     * @param array $infos
     * @param $client
     */
    function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);

        $this->lastUseAdd = 0;
        $this->lastUse = 0;
    }

    public function onConnect()
    {
        $this->getClient()->sendMessage("Don't die again, your deaths are counted !");
    }

    
    /**
     * @param \TwitchBot\Message $message
     */
    public function onMessage($message)
    {
        if (substr($message->getMessage(), 0, 4) == '!rip') {

            $function = explode(' ', $message->getMessage());
            $function = isset($function[1]) ? $function[1] : null;

            switch ($function) {
                case null:
                    $this->displayCounter();
                    break;
                case 'add':
                    if($message->getUserType() >= 2){
                        $this->incrementRip();
                    }
                    break;
                case 'reset':
                    if($message->getUserType() >= 2){
                        $this->resetRip();
                    }
                    break;
                default:
                    break;
            }
        }
    }


    /**
     * @return bool
     */
    private function displayCounter()
    {

        $delay = $this->getConfig('delay_use');

        if(time() - $this->lastUse < $delay){
            return true;
        }

        if ($this->getCounter() == 0) {
            $message = $this->getConfig('message_notdead');
            $message = sprintf($message, ucfirst($this->getInfo('channel')));
        } else {
            $message = $this->getConfig('message_display');
            $message = sprintf($message, ucfirst($this->getInfo('channel')), $this->getCounter());
        }

        $this->lastUse = time();

        $this->getClient()->sendMessage($message);

        return true;
    }

    /**
     * @return bool
     */
    private function incrementRip()
    {

        $delay = $this->getConfig('delay_add');

        if(time() - $this->lastUseAdd < $delay){
            return true;
        }

        $counter = $this->getCounter();
        $counter ++;
        $this->setCounter($counter);

        $this->lastUseAdd = time();

        $this->getClient()->sendMessage($this->getConfig('message_increment'));
        $this->displayCounter();

        return true;
    }

    /**
     * @return bool
     */
    private function resetRip()
    {
        $this->setCounter(0);

        $this->getClient()->sendMessage($this->getConfig('message_reset'));

        return true;
    }

    /**
     * @return int
     */
    private function getCounter()
    {
        $counter = file_get_contents(self::$COUNTERFILE);
        return (int)$counter;
    }

    /**
     * @param Int $number
     * @return bool
     */
    private function setCounter($number)
    {
        file_put_contents(self::$COUNTERFILE, $number);

        return true;
    }

}
