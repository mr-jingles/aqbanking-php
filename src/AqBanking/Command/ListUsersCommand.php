<?php

namespace AqBanking\Command;

use AqBanking\Command\AddUserCommand\UserAlreadyExistsException;
use AqBanking\Command\ShellCommandExecutor\DefectiveResultException;
use AqBanking\Command\ShellCommandExecutor\ResultAnalyzer;
use AqBanking\User;

class ListUsersCommand extends AbstractCommand
{
    const RETURN_VAR_NOT_FOUND = 4;

    /**
     * @return \DOMDocument|null
     */
    public function execute()
    {
        $shellCommand =
            $this->pathToAqHBCIToolBinary
            . ' listusers'
            . ' --xml';

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        if ($result->getReturnVar() === 4) {
            return null;
        }

        if ($result->getReturnVar() !== 0) {
            throw new \RuntimeException(
                'AqBanking exited with errors: ' . PHP_EOL
                . implode(PHP_EOL, $result->getErrors())
            );
        }

        $domDocument = new \DOMDocument();
        $domDocument->loadXML(implode(PHP_EOL, $result->getOutput()));

        return $domDocument;
    }
}
