<?php

/**
 * Class Quote
 */
class Quote
{

    public $quote;

    public $addedBy;

    public $addedAt;

    /**
     * Quote constructor.
     * @param $quote
     * @param $user
     * @param DateTime|string $addedAt
     */
    public function __construct($quote, $user, $addedAt = 'now')
    {
        $this->quote = $quote;
        $this->addedBy = $user;

        if (is_object($addedAt)) {
            $this->addedAt = new DateTime($addedAt->date);
        } else {
            $this->addedAt = new DateTime($addedAt);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $date = $this->addedAt->format('d-m-Y');
        return '"' . $this->quote . '" - ' . $date;
    }
}