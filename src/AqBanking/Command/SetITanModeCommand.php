<?php

namespace AqBanking\Command;

use AqBanking\Command\AddUserCommand\UserAlreadyExistsException;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\ExistingUser;
use AqBanking\User;

class SetITanModeCommand extends AbstractCommand
{
    /**
     * @param User $user
     * @throws AddUserCommand\UserAlreadyExistsException
     * @throws ShellCommandExecutor\DefectiveResultException
     */
    public function execute(ExistingUser $user, $mode)
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary
            . ' setitanmode'
            . ' --user=' . $user->getUniqueUserId()
            . ' -m ' . escapeshellcmd($mode);

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        $resultAnalyzer = new ResultAnalyzer();
        if ($resultAnalyzer->isDefectiveResult($result)) {
            throw new DefectiveResultException('Unexpected output on setting user itan mode', 0, null, $result, $shellCommand);
        }
    }
}
