<?php

namespace App\Entity;

use App\Database\Hydration\Strategies\DateTimeStrategy;
use App\Database\Hydration\Strategies\UserStrategy;
use DateTime;

class User implements Entity
{
    private int $id;
    private string $name;
    private string $firstName;
    private string $username;
    private string $password;
    private string $email;

    #[Hydrator(strategy: DateTimeStrategy::class)]
    private DateTime $birthDate;

    #[Hydrator(strategy: UserStrategy::class)]
    private ?User $parrain;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBirthDate(): DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(DateTime $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

}
