<?php

namespace AqBanking;

use Money\Money;

class Transaction implements Arrayable
{
    /**
     * @var Account
     */
    private $localAccount;

    /**
     * @var Account
     */
    private $remoteAccount;

    /**
     * @var string
     */
    private $purpose;

    /**
     * @var \DateTime
     */
    private $valutaDate;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * @var Money
     */
    private $value;

    public function __construct(
        Account $localAccount,
        Account $remoteAccount,
        $purpose,
        \DateTime $valutaDate,
        \DateTime $date,
        Money $value
    ) {
        $this->date = $date;
        $this->localAccount = $localAccount;
        $this->purpose = $purpose;
        $this->remoteAccount = $remoteAccount;
        $this->value = $value;
        $this->valutaDate = $valutaDate;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return \AqBanking\Account
     */
    public function getLocalAccount()
    {
        return $this->localAccount;
    }

    /**
     * @return string
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * @return \AqBanking\Account
     */
    public function getRemoteAccount()
    {
        return $this->remoteAccount;
    }

    /**
     * @return \Money\Money
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return \DateTime
     */
    public function getValutaDate()
    {
        return $this->valutaDate;
    }

    public function toArray()
    {
        return [
            'date' => $this->getDate()->format('Y-m-d H:i:s'),
            'localAccount' => $this->getLocalAccount()->toArray(),
            'purpose' => $this->getPurpose(),
            'remoteAccount' => $this->getRemoteAccount()->toArray(),
            'value' => $this->getValue()->getAmount(),
            'valutaDate' => $this->getValutaDate()->format('Y-m-d H:i:s')
        ];
    }
}
