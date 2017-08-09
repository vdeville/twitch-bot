<?php


/**
 * Class Setinfo
 */
class Setinfo
{
    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    private static $FILEJSON = __DIR__ . '/storage.json';

    private $storage;

    private $lastGetInfo;

    /**
     * Setinfo constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);

        $this->lastGetInfo = time() - $this->getConfig("delay_use");

        $this->storage = json_decode(file_get_contents(self::$FILEJSON));
    }

    public function onConnect()
    {
        if ($this->getInfo('connect_message')) {
            $this->getClient()->sendMessage('Plugin Setinfo activate !');
        }
    }

    /**
     * @param \TwitchBot\Command $command
     * @return bool
     */
    public function onCommand($command)
    {
        if ($command == "setinfo" AND $command->getMessage()->getUserType() == 3) {

            $info = substr($command->getCommandAndArgsRaw(), 8);

            $this->setInfo($info, $command->getMessage());

        } else if ($command == "info") {

            $args = $command->getArgs();
            $userToPing = (isset($args[1])) ? $args[1] : false;

            $this->sendResponse($userToPing);
        }

        return true;
    }


    /**
     * @param string $info
     * @param \TwitchBot\Message $message
     */
    private function setInfo($info, $message)
    {

        $storage = $this->getStorage();

        $storage->message = $info;

        $storage->setBy = $message->getUsername();
        $storage->setAt = $message->getDate()->format('Y-m-d H:i:s');

        $this->setStorage($storage);

        $message = sprintf($this->getConfig('message_newreply'));
        $this->getClient()->sendMessage($message);
    }

    /**
     * @param bool|string $userToPing
     */
    public function sendResponse($userToPing = false)
    {

        $diff = time() - $this->getLastGetInfo();

        if ($diff >= $this->getConfig("delay_use")) {

            if ($userToPing) {
                $message = sprintf($this->getConfig('message_reply_user'), $userToPing, $this->getInfoMessage());
            } else {
                $message = sprintf($this->getConfig('message_reply'), $this->getInfoMessage());
            }

            $this->getClient()->sendMessage($message);
            $this->setLastGetInfo(time());
        }

    }

    /**
     * @return int
     */
    private function getLastGetInfo()
    {
        return $this->lastGetInfo;
    }

    /**
     * @param $value
     * @return $this
     */
    private function setLastGetInfo($value)
    {
        $this->lastGetInfo = $value;

        return $this;
    }

    /**
     * @return string mixed
     */
    private function getInfoMessage()
    {
        $storage = $this->getStorage();
        return $storage->message;
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