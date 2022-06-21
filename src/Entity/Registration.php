<?php

namespace App\Entity;

use App\Database\Hydration\Strategies\DateTimeStrategy;
use App\Database\Hydration\Strategies\EventStrategy;
use App\Database\Hydration\Strategies\UserStrategy;
use DateTime;

class Registration implements Entity
{
    private int $id;

    #[Hydrator(strategy: EventStrategy::class)]
    private int $eventID;

    #[Hydrator(strategy: UserStrategy::class)]
    private int $userID;

    #[Hydrator(strategy: DateTimeStrategy::class)]
    private DateTime $registeredAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Registration
    {
        $this->id = $id;
        return $this;
    }

    public function getEventID(): int
    {
        return $this->eventID;
    }

    public function setEventID(int $eventID): Registration
    {
        $this->eventID = $eventID;
        return $this;
    }

    public function getUserID(): int
    {
        return $this->userID;
    }

    public function setUserID(int $userID): Registration
    {
        $this->userID = $userID;
        return $this;
    }

    public function getRegisteredAt(): DateTime
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(DateTime $registeredAt): Registration
    {
        $this->registeredAt = $registeredAt;
        return $this;
    }

}