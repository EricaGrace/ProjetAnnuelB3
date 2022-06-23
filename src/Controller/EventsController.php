<?php

namespace App\Controller;

use App\Repository\EventCategoryRepository;
use App\Repository\EventRepository;
use App\Routing\Attribute\Route;

class EventsController extends AbstractController
{

    #[Route(path: '/category/{slug}', httpMethod: 'GET', name: 'event.category')]
    public function categoryPage(EventCategoryRepository $eventCategoryRepository, string $slug)
    {
        $category = $eventCategoryRepository->findBySlug($slug);
        $events = $eventCategoryRepository->findEventsFromCategory($category->getId());

        return $this->renderIf('Evenement/EvenementCategorie.html.twig', [
            'category' => $category,
            'events' => $events
        ], $category);
    }

    #[Route(path: '/event/{slug}', httpMethod: 'GET', name: 'event')]
    public function show(EventRepository $repository, string $slug)
    {
        return $this->render('Evenement/DetailsEvenement.html.twig', [
            'event' => $repository->findBySlug($slug)
        ]);
    }
}