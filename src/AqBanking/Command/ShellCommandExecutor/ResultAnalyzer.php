<?php

namespace AqBanking\Command\ShellCommandExecutor;

class ResultAnalyzer
{
    private $expectedOutputRegexes = array(
        '/Automatically accepting valid new certificate/',
        '/Automatically accepting certificate/',
        '/The TLS connection was non-properly terminated./', // it usually automatically restarts, so no error
        '/Unexpected tag/',
        '/To debug set environment variable/',
        '/Your bank does not send an opening saldo/',
        '/Bank data for KtoBlzCheck not found/',
        '/Executing Jobs: Started\./',
        '/A TLS packet with unexpected length was received\./',
        // The following happens when using flag FLAG_SSL_QUIRK_IGNORE_PREMATURE_CLOSE for some banks
        '/Detected premature disconnect by server \(violates specs!\), ignoring\./',
        '/The TLS connection was non-properly terminated./',
        '/Bad IBAN \(country code not in upper case\)/',
        '/Adding flags/',
        '/You may see some messages like "Job not supported with this account" below, that\'s are okay, please ignore/',
        '/not supported with this account/',
        '/Account exists, modifying/',
        '/Handling user/',
        '/Writing account spec/',
        '/===== Executing Jobs =====/',
        '/===== Getting Certificate =====/',
        '/Handling user/',
        '/RXH-encrypting message/',
        '/^  .*$/', // everything starting with a space belongs to a previous message and is not an error (hopefully)
    );

    /**
     * @param Result $result
     * @return bool
     */
    public function isDefectiveResult(Result $result)
    {
        if ($result->getReturnVar() !== 0) {
            return true;
        }
        if ($error = $this->resultHasErrors($result)) {
            return true;
        }
        return false;
    }

    private function resultHasErrors(Result $result)
    {
        if (count($result->getErrors()) == 1 && preg_match('/accepting valid new certificate/', $result->getErrors()[0])) {
            // When calling getsysid with wrong PIN, we don't get any error message.
            // The only significant aspect of the error is that the output is just one line with
            // "accepting valid new certificate"
            return true;
        }
        foreach ($result->getErrors() as $line) {
            if ($this->isErrorMessage($line)) {
                return true;
            }
        }
        return false;
    }

    private function isErrorMessage($line)
    {
        foreach ($this->expectedOutputRegexes as $regex) {
            if (preg_match($regex, $line)) {
                return false;
            }
        }
        return true;
    }
}
