<?php

namespace App\Controller;

use App\Repository\EventCategoryRepository;
use App\Routing\Attribute\Route;

class EventsController extends AbstractController
{

    #[Route(path: '/category/{slug}', httpMethod: 'GET', name: 'event.category')]
    public function categoryPage(EventCategoryRepository $eventCategoryRepository, string $slug)
    {
        $category = $eventCategoryRepository->findBySlug($slug);

        return $this->twig->render('Evenement/EvenementCategorie.html.twig', [
            'category' => $category,
            'events' => $eventCategoryRepository->findEventsFromCategory($category->getId())
        ]);
    }
}