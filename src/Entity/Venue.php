<?php

namespace App\Entity;

class Venue implements Entity
{
    private int $id;
    private string $venueName;
    private string $streetNo;
    private string $streetName;
    private string $city;
    private int $postalCode;
    private string $country;
    private ?float $latitude;
    private ?float $longitude;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Venue
    {
        $this->id = $id;
        return $this;
    }

    public function getVenueName(): string
    {
        return $this->venueName;
    }

    public function setVenueName(string $venueName): Venue
    {
        $this->venueName = $venueName;
        return $this;
    }

    public function getStreetNo(): string
    {
        return $this->streetNo;
    }

    public function setStreetNo(string $streetNo): Venue
    {
        $this->streetNo = $streetNo;
        return $this;
    }

    public function getStreetName(): string
    {
        return $this->streetName;
    }

    public function setStreetName(string $streetName): Venue
    {
        $this->streetName = $streetName;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): Venue
    {
        $this->city = $city;
        return $this;
    }

    public function getPostalCode(): int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): Venue
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): Venue
    {
        $this->country = $country;
        return $this;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): Venue
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): Venue
    {
        $this->longitude = $longitude;
        return $this;
    }


}