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

    private static $DELAY = 30;

    private $lastGetInfo;

    /**
     * Setinfo constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);

        $this->lastGetInfo = time() - self::$DELAY;

        $this->storage = json_decode(file_get_contents(self::$FILEJSON));
    }

    public function onConnect()
    {
        $this->getClient()->sendMessage('Info plugin Activated !');
    }

    /**
     * @param \TwitchBot\Message $data
     */
    public function onMessage($data)
    {
        if($data->getMessage()[0] == '!'){

            $command = trim($data->getMessage());
            $command = substr($command, 1);
            $command = explode(' ',$command)[0];

            $command = strtolower($command);

            switch ($command){
                case 'setinfo':
                    ($data->getUserType() == 3) ? $this->setInfo($data) : false;
                    break;
                case 'info':
                    $this->sendResponse($data->getMessage());
                    break;
                default:
                    break;
            }

        }
    }

    /**
     * @param \TwitchBot\Message $data
     */
    private function setInfo($data){

        $storage = $this->getStorage();
        $message = substr($data->getMessage(), 9);

        $storage->message = $message;

        $storage->setBy = $data->getUsername();
        $storage->setAt = $data->getDate()->format('Y-m-d H:i:s');

        $this->setStorage($storage);

        $message = sprintf($this->getConfig('message_newreply'));
        $this->getClient()->sendMessage($message);
    }

    /**
     * @param $message
     */
    public function sendResponse($message){

        $diff = time() - $this->getLastGetInfo();

        if($diff >= self::$DELAY){

            $userToPing = explode(' ', $message);
            if(isset($userToPing[1])){
                $message = sprintf($this->getConfig('message_reply_user'), $userToPing[1], $this->getInfo());
                $this->getClient()->sendMessage($message);
            } else{
                $message = sprintf($this->getConfig('message_reply'), $this->getInfo());
                $this->getClient()->sendMessage($message);
            }

            $this->setLastGetInfo(time());
        }

    }

    /**
     * @return int
     */
    private function getLastGetInfo(){
        return $this->lastGetInfo;
    }

    /**
     * @param $value
     * @return $this
     */
    private function setLastGetInfo($value){
        $this->lastGetInfo = $value;

        return $this;
    }

    /**
     * @return string mixed
     */
    private function getInfo(){
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
    public function setStorage($storage){
        file_put_contents(self::$FILEJSON, json_encode($storage));
    }

}
