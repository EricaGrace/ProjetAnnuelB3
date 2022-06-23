<?php

namespace App\Repository;

use App\Entity\Event;

class EventRepository extends AbstractRepository
{
    protected const TABLE = 'events';
    protected const ENTITY = Event::class;
}