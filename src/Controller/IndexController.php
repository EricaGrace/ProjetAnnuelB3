<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Routing\Attribute\Route;
use DateTime;

class IndexController extends AbstractController
{
  #[Route(path: "/")]
  public function index(UserRepository $userRepository)
  {
    $user = new User();

    $user->setName("Bob")
      ->setFirstName("John")
      ->setUsername("Bobby")
      ->setPassword("randompass")
      ->setEmail("bob@bob.com")
      ->setBirthDate(new DateTime('1981-02-16'));

    $userRepository->save($user);
  }

  #[Route(path: "/contact", name: "contact", httpMethod: "POST")]
  public function contact()
  {
    echo $this->twig->render('index/contact.html.twig');
  }
}
