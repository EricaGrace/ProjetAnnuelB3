<?php

namespace App\Auth;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Session\SessionInterface;

class Authenticator
{

    private const SESSION_KEY = 'user_id';
    private ?int $userID;
    private bool $authenticated = false;
    private SessionInterface $session;
    private UserRepository $userRepository;

    public function __construct(SessionInterface $session, UserRepository $userRepository)
    {
        $this->session = $session;

        $this->init();
        $this->userRepository = $userRepository;
    }

    private function init()
    {
        if ($userID = $this->session->get(static::SESSION_KEY, false)) {
            $this->authenticate($userID);
        }
    }

    public function authenticate($userID): void
    {
        $this->session->set(static::SESSION_KEY, $userID);
        $this->setAuthenticated(true);
        $this->setUserID($userID);
    }

    public function logout(): void
    {
        $this->session->destroy();
        $this->setAuthenticated(false);
        $this->setUserID(null);
    }

    public function __invoke(): static
    {
        return $this;
    }

    protected function getUserID(): int
    {
        return $this->userID;
    }

    protected function setUserID(?int $userID): static
    {
        $this->userID = $userID;

        return $this;
    }

    public function isAuthenticated(): bool
    {
        return $this->authenticated;
    }

    public function setAuthenticated(bool $authenticated): static
    {
        $this->authenticated = $authenticated;

        return $this;
    }

    public function getAuthenticatedUser(): bool|User
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        return $this->userRepository->find($this->getUserID());
    }
}