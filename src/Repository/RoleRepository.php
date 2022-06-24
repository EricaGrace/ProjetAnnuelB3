<?php

namespace App\Repository;

use App\Entity\Role;

final class RoleRepository extends AbstractRepository
{
    protected const TABLE = 'roles';
    protected const ENTITY = Role::class;
}
