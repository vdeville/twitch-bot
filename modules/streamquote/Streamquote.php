<?php


/**
 * Class Streamquote
 */
class Streamquote
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

        foreach ($storages as $storage) {
            $quote = new Quote($storage->quote, $storage->addedBy, $storage->addedAt);
            $this->storage[] = $quote;
        }
    }

    /**
     * @param \TwitchBot\Command $command
     * @return bool
     */
    public function onCommand($command)
    {
        if ($command == "quote" AND $command->getMessage()->getUserType() > 0) {

            $this->getQuote();

        } else if ($command == "addquote" AND $command->getMessage()->getUserType() >= 2) {
            $quote = substr($command->getCommandAndArgsRaw(), 9);

            $this->addQuote($quote, $command->getMessage());
        }

        return true;
    }

    /**
     * @param string $quote
     * @param \TwitchBot\Message $message
     * @internal param \TwitchBot\Message $data
     */
    private function addQuote($quote, $message)
    {
        $storage = $this->getStorage();

        $quote = new Quote($quote, $message->getUsername(true));
        $storage[] = $quote;

        $this->setStorage($storage);

        $message = $this->getConfig('add_quote_message');
        $this->getClient()->sendMessage($message);
    }

    public function getQuote()
    {
        $storage = $this->getStorage();
        $number = count($storage) - 1;
        $random = rand(0, $number);

        $this->getClient()->sendMessage($storage[$random]);
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
    public function setStorage($storage)
    {
        $this->storage = $storage;

        file_put_contents(self::$FILEJSON, json_encode($storage, JSON_PRETTY_PRINT));
    }

}
