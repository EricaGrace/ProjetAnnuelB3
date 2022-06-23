<?php

namespace App\Repository;

use App\Entity\EventCategory;

final class EventCategoryRepository extends AbstractRepository
{
    protected const TABLE = 'event_categories';
    protected const ENTITY = EventCategory::class;
}
