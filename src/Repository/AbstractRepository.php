<?php

namespace App\Repository;

use App\Database\Hydration\HydratorInterface;
use App\Entity\Entity;
use PDO;

abstract class AbstractRepository
{
    protected const TABLE = '';
    protected const ENTITY = '';
    protected PDO $pdo;
    protected HydratorInterface $hydrator;

    public function __construct(PDO $pdo, HydratorInterface $hydrator)
    {
        $this->pdo = $pdo;
        $this->hydrator = $hydrator;
    }

    public function find(int $id): ?Entity
    {
        $stmt = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE id=:id");

        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $this->hydrate($result);
    }

    protected function hydrate($values): Entity
    {
        $entity = static::ENTITY;
        return $this->hydrator->hydrate($values, new $entity());
    }
}
