<?php

namespace App\Repository;

use App\Entity\Event;
use App\Repository\Traits\EntityHasSlug;

class EventRepository extends AbstractRepository
{
    use EntityHasSlug;

    public const TABLE = 'events';
    protected const ENTITY = Event::class;

    public function save(Event $event)
    {
        $stmt = $this->pdo->prepare("INSERT INTO " . static::TABLE . " (title, category, slug, date, maxAttendees, creator, price, description, image ) VALUES (:title, :category, :slug, :date, :maxAttendees, :creator, :price, :description, :image )");

        return $stmt->execute([
            'title' => $event->getTitle(),
            'category' => $event->getCategory()->getId(),
            'slug' => $event->getSlug(),
            'date' => $event->getDate()->format('Y-m-d'),
            'maxAttendees' => $event->getMaxAttendees(),
            'creator' => $event->getCreator()->getId(),
            'price' => $event->getPrice(),
            'description' => $event->getDescription(),
            'image' => $event->getImage()
        ]);
    }
}