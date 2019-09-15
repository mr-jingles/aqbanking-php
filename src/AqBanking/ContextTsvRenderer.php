<?php

namespace AqBanking;

use AqBanking\ContentXmlRenderer\MoneyElementRenderer;
use Money\Currency;
use Money\Money;

class ContextTsvRenderer
{
    /**
     * @var string
     */
    private $tsvData;

    /**
     * @var MoneyElementRenderer
     */
    private $moneyElementRenderer;

    public function __construct(array $tsvData)
    {
        $this->tsvData = $tsvData;
        $this->moneyElementRenderer = new MoneyElementRenderer();
    }

    /**
     * @return Transaction[]
     */
    public function getTransactions()
    {
        $fileData = implode("\n", $this->tsvData);

        $data = str_getcsv($fileData, "\n");
        $rows = [];
        foreach($data as $row) {
            $rowDecoded = array_combine(\AqBanking\Command\RenderContextFileToTsvCommand::FIELD_LIST, str_getcsv($row, "\t"));
            if ($rowDecoded !== false) { // FIXME: Happens during multiline parsing
                $rows[] = $rowDecoded;
            }
        }

        var_dump($rows);

        foreach ($rows as $row) {
            $localBankCode = $row['localBankcode'];
            $localAccountNumber = $row['localAccountnumber'];
            $localName = '';
            $remoteBankCode = $row['remoteBankcode'];
            $remoteAccountNumber = $row['remoteAccountnumber'];
            $remoteName = $row['remoteName'];
            $purpose = $row['purpose'];
            $valutaDate = $this->renderDateElement($row['valutaDateAsString']);
            $date = $this->renderDateElement($row['dateAsString']);
            $value = $this->renderMoneyElement($row['valueAsString']);

            $transactions[] = new Transaction(
                new Account(new BankCode($localBankCode), $localAccountNumber, $localName),
                new Account(new BankCode($remoteBankCode), $remoteAccountNumber, $remoteName),
                $purpose,
                $valutaDate,
                $date,
                $value
            );
        }

        return $transactions;
    }

    /**
     * @return Money
     */
    public function getBalance()
    {
        $statusNode = $this->domDocument->getElementsByTagName('bookedBalance')->item(0);

        return $this->renderMoneyElement(
            $this->xPath->query('value', $statusNode)->item(0)
        );
    }

    /**
     * @param \DOMNode $node
     * @param \DOMNode $node
     * @throws \RuntimeException
     * @return \DateTime
     */
    private function renderDateElement($dateAsString)
    {
        $date = \DateTime::createFromFormat('d.m.Y', $dateAsString);

        if (!$date) {
            return null;
        }

        return $date;
    }

    /**
     * @param \DOMNode $node
     * @return Money
     * @throws \Exception
     */
    private function renderMoneyElement($valueString)
    {
        // FIXME: find a way to represent the currency - maybe hard code in account?
        $currency = new Currency('EUR');
        $amount = (integer)($valueString * 100);

        return new Money($amount, $currency);
    }
}
