<?php

namespace App\Entity;

use App\Database\Hydration\Strategies\DateTimeStrategy;
use App\Database\Hydration\Strategies\EventCategoryStrategy;
use App\Database\Hydration\Strategies\UserStrategy;
use App\Database\Hydration\Strategies\VenueStrategy;
use DateTime;

class Event implements Entity
{
    private int $id;
    private string $title;
    private string $slug;

    #[Hydrator(strategy: EventCategoryStrategy::class)]
    private EventCategory $category;
    private string $description;
    private string $image;

    #[Hydrator(strategy: DateTimeStrategy::class)]
    private DateTime $date;
    private int $maxAttendees;
    private float $price;

    #[Hydrator(strategy: UserStrategy::class)]
    private User $creator;

    #[Hydrator(strategy: VenueStrategy::class)]
    private ?Venue $venue;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Event
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Event
    {
        $this->title = $title;
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): Event
    {
        $this->slug = $slug;
        return $this;
    }

    public function getCategory(): EventCategory
    {
        return $this->category;
    }

    public function setCategory(EventCategory $category): Event
    {
        $this->category = $category;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Event
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): Event
    {
        $this->image = $image;
        return $this;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): Event
    {
        $this->date = $date;
        return $this;
    }

    public function getMaxAttendees(): int
    {
        return $this->maxAttendees;
    }

    public function setMaxAttendees(int $maxAttendees): Event
    {
        $this->maxAttendees = $maxAttendees;
        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): Event
    {
        $this->price = $price;
        return $this;
    }

    public function getCreator(): User
    {
        return $this->creator;
    }

    public function setCreator(User $creator): Event
    {
        $this->creator = $creator;
        return $this;
    }

    public function getVenue(): Venue
    {
        return $this->venue;
    }

    public function setVenue(Venue $venue): Event
    {
        $this->venue = $venue;
        return $this;
    }


}