<?php

namespace AqBanking;

/**
 * Find user in existing user database
 * @package AqBanking
 */
class UserMatcher
{
    /**
     * @var \DOMDocument
     */
    private $domDocument;

    public function __construct(\DOMDocument $domDocument = null)
    {
        $this->domDocument = $domDocument;
        if ($domDocument !== null) {
            $this->xPath = new \DOMXPath($domDocument);
        }
    }

    public function getExistingUser(User $user)
    {
        if ($this->domDocument === null) {
            return null;
        }

        $userNodes = $this->xPath->query('/users/user', $this->domDocument);

        foreach ($userNodes as $userNode) {
            $userName = $userNode->getElementsByTagName('UserName')[0]->nodeValue;
            $userId = $userNode->getElementsByTagName('UserId')[0]->nodeValue;
            $customerId = $userNode->getElementsByTagName('CustomerId')[0]->nodeValue;
            $bankCode = $userNode->getElementsByTagName('BankCode')[0]->nodeValue;
            $uniqueId = $userNode->getElementsByTagName('userUniqueId')[0]->nodeValue;

            if (
                $user->getUserName() == $userName &&
                $user->getUserId() == $userId &&
                $user->getBank()->getBankCode()->getString() == $bankCode
            ) {
                return new ExistingUser($user, $uniqueId);
            }
        }

        return null;
    }
}

