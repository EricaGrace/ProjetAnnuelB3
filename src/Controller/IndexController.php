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
        return $this->twig->render('index.html.twig', [
            'request' => $request,
            'events' => $eventRepository->findAll()
        ]);
    }

    #[Route(path: "/contact", httpMethod: "GET", name: "contact")]
    public function contactForm()
    {
        return $this->twig->render('index/contact.html.twig');
    }

    #[Route(path: "/contact", httpMethod: "POST", name: "handleContact")]
    public function handleContactForm(Request $request)
    {
        $query = $request->request->all();

        return $this->twig->render('index/contact.html.twig');
    }


    #[Route(path: "/login", httpMethod: "GET", name: "login")]
    public function login()
    {
        return $this->twig->render('User/login.html.twig');
    }

    #[Route(path: "/compte", httpMethod: "GET", name: "account")]
    public function inscription()
    {
        return $this->twig->render('User/MonCompte.html.twig');
    }

    #[Route(path: "/add", httpMethod: "GET", name: "evenement")]
    public function add()
    {
        return $this->twig->render('Administration/AjouterEvenement.html.twig');
    }
}
  
