<?php

namespace App\Repository;

use App\Entity\Event;
use App\Repository\Traits\EntityHasSlug;

class EventRepository extends AbstractRepository
{
    use EntityHasSlug;

    public const TABLE = 'events';
    protected const ENTITY = Event::class;
}