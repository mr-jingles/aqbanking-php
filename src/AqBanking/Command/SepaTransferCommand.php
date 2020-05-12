<?php

namespace AqBanking\Command;

use AqBanking\Account;
use AqBanking\ContextFile;
use AqBanking\PinFile\PinFile;
use AqBanking\Command\AbstractCommand;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;

class SepaTransferCommand extends AbstractCommand {

    /**
     * @var Account
     */
    private $account;

    /**
     * @var ContextFile
     */
    private $contextFile;

    /**
     * @var PinFile
     */
    private $pinFile;

    /**
     * @param Account $account
     * @param ContextFile $contextFile
     * @param PinFile $pinFile
     */
    public function __construct(Account $account, ContextFile $contextFile, PinFile $pinFile)
    {
        $this->account = $account;
        $this->contextFile = $contextFile;
        $this->pinFile = $pinFile;
    }



    /**
     * @param string $rname remote name
     * @param string $riban remote iban
     * @param string $value value to transfer "1/100:EUR"
     * @param string $purpose purpose of the transfer
     * @throws ShellCommandExecutor\DefectiveResultException
     */
    public function execute(string $rname, string $riban, string $value, string $purpose, \DateTime $executionDate = null)
    {
        $shellCommand = $this->getShellCommand($rname, $riban, $value, $purpose, $executionDate);
        $result = $this->getShellCommandExecutor()->execute($shellCommand);
        $resultAnalyzer = new ResultAnalyzer();
        if ($resultAnalyzer->isDefectiveResult($result)) {
            throw new DefectiveResultException(
                'Unexpected output on polling transactions',
                0,
                null,
                $result,
                $shellCommand
            );
        }
    }

    /**
     * @param string $rname remote name
     * @param string $riban remote iban
     * @param string $value value to transfer "1/100:EUR"
     * @param string $purpose purpose of the transfer
     * @return string
     */
    private function getShellCommand(string $rname, string $riban, string $value, string $purpose, \DateTime $executionDate = null)
    {
        $shellCommand =
            $this->pathToAqBankingCLIBinary
            . " --noninteractive"
            . " --acceptvalidcerts"
            . " --pinfile=" . escapeshellcmd($this->pinFile->getPath())
            . " sepatransfer"
            . " --bank=" . escapeshellcmd($this->account->getBankCode()->getString())
            . " --account=" . escapeshellcmd($this->account->getAccountNumber())
            . " --ctxfile=" . escapeshellcmd($this->contextFile->getPath())
            . " --rname='" . escapeshellcmd($rname) . "'"
            . " --riban=" .  escapeshellcmd($riban)
            . " --value=" . escapeshellcmd($value)
            . " --purpose='" . escapeshellcmd($purpose) . "'"
        ;

        if (null !== $executionDate) {
            $shellCommand .= " --execdate=" . $executionDate->format('Ymd');
        }

        return $shellCommand;
    }
}
