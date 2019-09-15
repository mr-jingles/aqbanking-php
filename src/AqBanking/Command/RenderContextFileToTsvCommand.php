<?php

namespace AqBanking\Command;

use AqBanking\ContextFile;

class RenderContextFileToTsvCommand extends AbstractCommand
{
    const FIELD_LIST = ['localBankcode', 'localAccountnumber', 'remoteBankcode', 'remoteAccountnumber',
        'remoteName', 'purpose', 'valutaDateAsString', 'dateAsString', 'valueAsString'];

    private function buildTemplate()
    {
        $templateVars = [];
        foreach(self::FIELD_LIST as $field) {
            $templateVars[] = '$(' . $field . ')';
        }
        return implode('\t', $templateVars);
    }
    /**
     * @param ContextFile $contextFile
     * @return array
     * @throws \RuntimeException
     */
    public function execute(ContextFile $contextFile)
    {
        $shellCommand =
            $this->pathToAqBankingCLIBinary
            . ' listtrans'
            . ' --ctxfile=' . escapeshellarg($contextFile->getPath())
            . ' --template=' . escapeshellarg($this->buildTemplate());

        $result = $this->getShellCommandExecutor()->execute($shellCommand);

        if ($result->getReturnVar() !== 0) {
            throw new \RuntimeException(
                'AqBanking exited with errors: ' . PHP_EOL
                . implode(PHP_EOL, $result->getErrors())
            );
        }

        return $result->getOutput();
    }
}
