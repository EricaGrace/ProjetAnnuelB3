<?php

namespace App\Entity;

class EventCategory implements Entity
{
    private int $id;
    private string $name;
    private string $slug;

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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): EventCategory
    {
        $this->slug = $slug;
        return $this;
    }

}