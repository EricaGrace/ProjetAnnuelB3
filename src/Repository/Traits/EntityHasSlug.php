<?php

namespace App\Repository\Traits;

use App\Entity\Entity;
use PDO;

trait EntityHasSlug
{

    public function findBySlug(string $slug): ?Entity
    {
        $stmt = $this->pdo->prepare("SELECT * FROM " . static::TABLE . " WHERE slug=:slug");

        $stmt->execute(['slug' => $slug]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($result !== false) ? $this->hydrate($result) : null;
    }
}