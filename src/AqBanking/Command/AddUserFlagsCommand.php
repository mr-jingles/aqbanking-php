<?php

namespace AqBanking\Command;

use AqBanking\Command\AddUserCommand\UserAlreadyExistsException;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\ExistingUser;
use AqBanking\User;

class AddUserFlagsCommand extends AbstractCommand
{
    const FLAG_SSL_QUIRK_IGNORE_PREMATURE_CLOSE = 'tlsIgnPrematureClose';

    /**
     * @param User $user
     * @throws AddUserCommand\UserAlreadyExistsException
     * @throws ShellCommandExecutor\DefectiveResultException
     */
    public function execute(ExistingUser $user, $flags)
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary
            . ' adduserflags'
            . ' --user=' . $user->getUniqueUserId()
            . ' --flags=' . escapeshellcmd($flags);

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        $resultAnalyzer = new ResultAnalyzer();
        if ($resultAnalyzer->isDefectiveResult($result)) {
            throw new DefectiveResultException('Unexpected output on setting user flags', 0, null, $result, $shellCommand);
        }
    }
}
