<?php

namespace App\Repository;

use PDO;

abstract class AbstractRepository
{
  protected PDO $pdo;
  protected const TABLE = '';

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function find(int $id): array
  {
    $stmt = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE id=:id");

    $stmt->execute(['id' => $id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return ($result !== false) ? $result : [];
  }
}
