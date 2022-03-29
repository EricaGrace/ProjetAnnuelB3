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
    echo $this->twig->render('index/home.html.twig', [
        'request' => $request
    ]);
  }

  #[Route(path: "/contact", httpMethod: "GET", name: "contact")]
  public function contact()
  {
    echo $this->twig->render('index/contact.html.twig');
  }
}
