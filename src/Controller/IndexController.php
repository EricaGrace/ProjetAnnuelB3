<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
    #[Route(path: "/", httpMethod: "GET", name: 'home')]
    public function index(Request $request, EventRepository $eventRepository)
    {
        $events = $eventRepository->findAll();

        return $this->renderIf('index.html.twig', [
            'request' => $request,
            'events' => $events
        ], $events);
    }

    #[Route(path: "/contact", httpMethod: "GET", name: "contact")]
    public function contactForm()
    {
        return $this->render('index/contact.html.twig');
    }

    #[Route(path: "/contact", httpMethod: "POST", name: "handleContact")]
    public function handleContactForm(Request $request)
    {
        $query = $request->request->all();

        return $this->render('index/contact.html.twig');
    }

    #[Route(path: "/add", httpMethod: "GET", name: "evenement")]
    public function add()
    {
        return $this->render('Administration/AjouterEvenement.html.twig');
    }
}
  
