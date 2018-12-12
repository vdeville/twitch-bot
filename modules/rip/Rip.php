<?php

use TwitchBot\Utils;

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
        if ($this->getInfo('connect_message')) {
            $this->getClient()->sendMessage("Don't die again ! Your deaths are counted !");
        }
    }

    
    /**
     * @param \TwitchBot\Command $command
     * @return bool
     */
    public function onCommand($command)
    {
        if ($command == "rip") {

            $args = $command->getArgs();

            $message = $command->getMessage();

            if (isset($args[1]) AND (Utils::isOwner($message) OR Utils::isMod($message))) {

                switch ($args[1]) {
                    case 'add':
                        $this->incrementRip();
                        break;
                    case 'reset':
                        $this->resetRip();
                        break;
                    default:
                        $this->getClient()->sendMessage('Invalid usage for rip command. Usage: rip add/reset');
                        break;
                }

            } else {
                $this->displayCounter();
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    private function displayCounter()
    {

        $delay = $this->getConfig('delay_use');

        if (time() - $this->lastUse < $delay) {
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

        if (time() - $this->lastUseAdd < $delay) {
            return true;
        }

        $counter = $this->getCounter();
        $counter++;
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
