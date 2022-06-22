<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Routing\Attribute\Route;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends AbstractController
{
  #[Route(path: "/", httpMethod: "GET")]
  public function index(Request $request)
  {
    return $this->twig->render('index.html.twig', [
        'request' => $request
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

  
  #[Route(path: "/login", name: "login", httpMethod: "GET")]
  public function login()
  {
    return $this->twig->render('connexion/login.html.twig');
  }

  #[Route(path: "/compte", name: "MonCompte", httpMethod: "GET")]
  public function inscription()
  {
    return $this->twig->render('MonCompte/MonCompte.html.twig');
  }
}
  
