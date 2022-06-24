<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Repository\Traits\EntityHasSlug;
use PDO;

final class EventCategoryRepository extends AbstractRepository
{
    use EntityHasSlug;

    protected const TABLE = 'event_categories';
    protected const ENTITY = EventCategory::class;

    public function findEventsFromCategory(int $id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM " . EventRepository::TABLE . " WHERE category=:id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function($values) {
            return $this->hydrate($values, Event::class);
        }, $result);
    }
}
