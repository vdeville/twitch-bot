<?php


/**
 * Class Warthsquote
 */
class Warthsquote
{
    use \TwitchBot\Module {
        \TwitchBot\Module::__construct as private moduleConstructor;
    }

    private static $FILEJSON = __DIR__ . '/storage.json';

    private $storage;

    /**
     * Warthsquote constructor.
     * @param array $infos
     * @param \TwitchBot\IrcConnect $client
     */
    public function __construct(array $infos, $client)
    {
        $this->moduleConstructor($infos, $client);

        $storages = json_decode(file_get_contents(self::$FILEJSON));
        foreach ($storages as $storage){
            $quote = new Quote($storage->quote, $storage->addedBy, $storage->addedAt);
            $this->storage[] = $quote;
        }
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
                case 'addquote':
                    ($data->getUserType() >= 2) ? $this->addQuote($data) : false;
                    break;
                case 'quote':
                    ($data->getUserType() > 0) ? $this->getQuote() : false;
                    break;
                default:
                    break;
            }

        }
    }

    /**
     * @param \TwitchBot\Message $data
     */
    private function addQuote($data){

        $storage = $this->getStorage();
        $message = substr($data->getMessage(), 10);

        $quote = new Quote($message, $data->getUsername());
        $storage[] = $quote;

        $this->setStorage($storage);

		$message = $this->getConfig('add_quote_message');
        $this->getClient()->sendMessage($message);
    }

    public function getQuote(){
        $storage = $this->getStorage();
        $number = count($storage) -1;
        $random = rand(0, $number);

        $this->getClient()->sendMessage($storage[$random]->getQuote());
    }

    /**
     * @return Quote[]
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param Quote[] $storage
     */
    public function setStorage($storage){
        $this->storage = $storage;

        file_put_contents(self::$FILEJSON, json_encode($storage, JSON_PRETTY_PRINT));
    }

}
