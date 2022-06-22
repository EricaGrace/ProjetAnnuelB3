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
    echo $this->twig->render('index.html.twig', [
        'request' => $request
    ]);
  }

  #[Route(path: "/contact", httpMethod: "GET", name: "contact")]
  public function contactForm()
  {
    echo $this->twig->render('index/contact.html.twig');
  }

  #[Route(path: "/contact", httpMethod: "POST", name: "handleContact")]
  public function handleContactForm(Request $request)
  {
    $query = $request->request->all();

    echo '<pre>';
    var_dump($query);
    echo '</pre>';
    echo $this->twig->render('index/contact.html.twig');
  }

  
  #[Route(path: "/inscription", name: "login", httpMethod: "GET")]
  public function login()
  {
    echo $this->twig->render('User/inscription.html.twig');
  }

  #[Route(path: "/compte", name: "MonCompte", httpMethod: "GET")]
  public function inscription()
  {
    echo $this->twig->render('MonCompte/MonCompte.html.twig');
  }
}
  
