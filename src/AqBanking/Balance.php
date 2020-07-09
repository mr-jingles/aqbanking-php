<?php

namespace AqBanking;

use Money\Money;

class Balance implements Arrayable
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var Money
     */
    private $value;

    public function __construct(
        \DateTime $date,
        Money $value,
        string $type
    ) {
        $this->date = $date;
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return \Money\Money
     */
    public function getValue()
    {
        return $this->value;
    }

    public function toArray()
    {
        return [
            'type' => $this->getType(),
            'value' => [
                'amount' => $this->getValue()->getAmount(),
                'priceUnit' => 100,
                'currency' => $this->getValue()->getCurrency()->getName()
            ],
            'date' => $this->getDate()->format('Y-m-d')
        ];
    }
}
