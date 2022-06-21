<?php

namespace App\Entity;

class EventCategory implements Entity
{
    private int $id;
    private string $name;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(int $id): EventCategory
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): EventCategory
    {
        $this->name = $name;
        return $this;
    }


}