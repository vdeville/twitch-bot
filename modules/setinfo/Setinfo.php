<?php


/**
 * Class Setinfo
 */
class Setinfo
{
    use \TwitchBot\Module;

    private static $FILEJSON = __DIR__ . '/storage.json';

    private $storage;

    /**
     * Setinfo constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->client = $client;
        $this->infos = $infos;

        $this->storage = json_decode(file_get_contents(self::$FILEJSON));
    }

    public function onConnect()
    {
        $this->getClient()->sendMessage('Plugin Setinfo activate !');
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
                    $explode = explode(' ', $data->getMessage());
                    if(isset($explode[1])){
                        $this->getClient()->sendMessage($explode[1] . ', ' . $this->getInfo());
                    } else{
                        $this->getClient()->sendMessage($this->getInfo());
                    }
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

        $this->getClient()->sendMessage('New response for !info command');
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