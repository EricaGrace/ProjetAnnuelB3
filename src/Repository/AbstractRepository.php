<?php

namespace App\Repository;

use App\Database\Hydration\HydratorInterface;
use App\Entity\Entity;
use LogicException;
use PDO;
use ReflectionClass;

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

        return ($result !== false) ? $this->hydrate($result) : null;
    }

    protected function hydrate($values, string $entity = null): Entity
    {
        $entity ??= static::ENTITY;

        if (!(new ReflectionClass($entity))->implementsInterface(Entity::class)) {
            throw new LogicException("Cannot hydrate something that's not an entity");
        }

        return $this->hydrator->hydrate($values, new $entity());
    }

    public function findAll(?int $limit = null)
    {
        $maxRows = $limit ? " LIMIT :limit" : null;
        $query = "SELECT * FROM " . static::TABLE . $maxRows;

        $stmt = $this->pdo->prepare($query);

        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }

        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($values) {
            return $this->hydrate($values);
        }, $result);
    }
}
