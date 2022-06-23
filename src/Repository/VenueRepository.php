<?php

namespace App\Repository;

use App\Entity\Venue;

final class VenueRepository extends AbstractRepository
{
    protected const TABLE = 'venues';
    protected const ENTITY = Venue::class;
}
