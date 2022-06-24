<?php

namespace App\Controller;

use App\Repository\EventCategoryRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class should not extend AbstractController
 */
class GlobalController
{

    private EventCategoryRepository $eventCategoryRepository;

    public function __construct(EventCategoryRepository $eventCategoryRepository)
    {
        $this->eventCategoryRepository = $eventCategoryRepository;
    }

    public function getGlobalData(): array
    {
        return [
            'categories' => $this->eventCategoryRepository->findAll(),
            'hello' => 'tesst'
        ];
    }
}