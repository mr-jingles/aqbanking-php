<?php

namespace AqBanking\Command;

use AqBanking\Bank;
use AqBanking\BankCode;
use AqBanking\Command\ShellCommandExecutor\Result;
use AqBanking\ExistingUser;
use AqBanking\PinFile\PinFile;
use AqBanking\User;

class GetSysIDCommandTest extends ShellCommandTestCase
{
    public function testPollsSysID()
    {
        $userId = 'mustermann';
        $uniqueUserId = 123;
        $userName = 'Max Mustermann';
        $bankCodeString = '12345678';
        $hbciUrl = 'https://hbci.example.com';

        $bankCode = new BankCode($bankCodeString);
        $bank = new Bank($bankCode, $hbciUrl);
        $user = new User($userId, $userName, $bank);
        $existingUser = new ExistingUser($user, $uniqueUserId);
        $pinFile = new PinFile('/path/to/pinfile/dir', $user);

        $shellCommandExecutorMock = $this->getShellCommandExecutorMock();

        $expectedCommand =
            'aqhbci-tool4'
            . ' --pinfile=' . escapeshellarg($pinFile->getPath())
            . ' --noninteractive'
            . ' --acceptvalidcerts'
            . ' getsysid'
            . ' --user=' . $uniqueUserId
        ;

        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->with($expectedCommand)
            ->andReturn(new Result(array(), array(), 0));

        $sut = new GetSysIDCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);
        $sut->execute($existingUser, $pinFile);

        // To satisfy PHPUnit's "strict" mode - if Mockery didn't throw an exception until here, everything is fine
        $this->assertTrue(true);
    }

    /**
     * @expectedException \AqBanking\Command\ShellCommandExecutor\DefectiveResultException
     */
    public function testThrowsExceptionOnUnexpectedResult()
    {
        $userId = 'mustermann';
        $uniqueUserId = 123;
        $userName = 'Max Mustermann';
        $bankCodeString = '12345678';
        $hbciUrl = 'https://hbci.example.com';

        $bankCode = new BankCode($bankCodeString);
        $bank = new Bank($bankCode, $hbciUrl);
        $user = new User($userId, $userName, $bank);
        $existingUser = new ExistingUser($user, $uniqueUserId);

        $pinFile = new PinFile('/path/to/pinfile/dir', $user);

        $shellCommandExecutorMock = $this->getShellCommandExecutorMock();

        $expectedCommand =
            'aqhbci-tool4'
            . ' --pinfile=' . escapeshellarg($pinFile->getPath())
            . ' --noninteractive'
            . ' --acceptvalidcerts'
            . ' getsysid'
            . ' --user=' . $uniqueUserId
        ;

        $shellCommandExecutorMock
            ->shouldReceive('execute')->once()
            ->with($expectedCommand)
            ->andReturn(new Result(array(), array(), 1));

        $sut = new GetSysIDCommand();
        $sut->setShellCommandExecutor($shellCommandExecutorMock);
        $sut->execute($existingUser, $pinFile);
    }
}
