<?php

namespace AqBanking;

/**
 * Should be greated by list users and user matcher
 *
 * @package AqBanking
 */
class ExistingUser
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    private $uniqueUserId;

    /**
     * @param string $userId
     * @param string $userName
     * @param Bank $bank
     */
    public function __construct(User $user, int $uniqueUserId)
    {
        $this->user= $user;
        $this->uniqueUserId = $uniqueUserId;
    }

    public function getUniqueUserId()
    {
        return $this->uniqueUserId;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->user->getUserId();
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->user->getUserName();
    }

    /**
     * @return Bank
     */
    public function getBank()
    {
        return $this->user->getBank();
    }
}
