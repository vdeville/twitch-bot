<?php

use TwitchBot\Utils;

/**
 * Class Setnext
 */
class Setnext
{
    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    private static $FILEJSON = __DIR__ . '/storage.json';

    private $storage;

    private $delay;

    private $lastGetNext;

    /**
     * Setnext constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);

        $this->delay = $this->getConfig('delay');

        $this->lastGetNext = time() - $this->delay;

        $this->storage = json_decode(file_get_contents(self::$FILEJSON));
    }

    /**
     * @param \TwitchBot\Command $command
     */
    public function onCommand($command)
    {
        switch ($command->getCommand()) {
            case 'setnext':
                (Utils::isOwner($command->getMessage())) ? $this->setNext($command) : false;
                break;
            case 'sethour':
                (Utils::isOwner($command->getMessage())) ? $this->setNext($command) : false;
                break;
            case 'next':
                $this->sendResponse($command);
                break;
            default:
                break;
        }
    }

    /**
     * @param \TwitchBot\Command $command
     */
    private function setNext($command)
    {

        $storage = $this->getStorage();
        $message = substr($command->getMessage()->getMessage(), 9);

        if ($command->getCommand() == 'sethour') {
            $storage->hour = $message;
        } else {
            $storage->message = $message;
        }

        $storage->setBy = $command->getMessage()->getUsername();
        $storage->setAt = $command->getMessage()->getDate()->format('Y-m-d H:i:s');

        $this->setStorage($storage);

        $this->getClient()->sendMessage($this->getConfig('message_new_next'));
    }

    /**
     * @param \TwitchBot\Command $command
     */
    public function sendResponse($command)
    {

        $diff = time() - $this->getLastGetNext();

        if ($diff >= $this->delay OR
            (Utils::isOwner($command->getMessage()) OR Utils::isMod($command->getMessage()))) {

            $userToPing = false;

            if (count($command->getArgs()) == 2) {
                $userToPing = $command->getArgs()[1];
            }

            if (false != $userToPing) {
                $msg = $this->getConfig('message_reply_user');
                $msg = sprintf($msg, $userToPing, $this->getNext());
                $this->getClient()->sendMessage($msg);
            } else {
                $msg = $this->getConfig('message_reply');
                $msg = sprintf($msg, $this->getNext());
                $this->getClient()->sendMessage($msg);
            }

            $this->setLastGetNext(time());
        }

    }

    /**
     * @return int
     */
    private function getLastGetNext()
    {
        return $this->lastGetNext;
    }

    /**
     * @param $value
     * @return $this
     */
    private function setLastGetNext($value)
    {
        $this->lastGetNext = $value;

        return $this;
    }

    /**
     * @return string mixed
     */
    private function getNext()
    {
        $storage = $this->getStorage();
        $message = $this->getConfig('next_reply');
        $message = sprintf($message, $storage->message, $storage->hour);
        return $message;
    }

    /**
     * @return stdClass mixed
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param stdClass $storage
     */
    public function setStorage($storage)
    {
        file_put_contents(self::$FILEJSON, json_encode($storage));
    }

}
