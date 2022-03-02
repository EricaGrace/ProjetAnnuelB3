<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Routing\Attribute\Route;
use App\Session\SessionInterface;

class UserController extends AbstractController
{
  #[Route(path: "/users", name: "users_list")]
  public function list(SessionInterface $session)
  {
    $users = [];

    echo $this->twig->render(
      'user/list.html.twig',
      [
        'users' => $users,
        'filter' => $session->get('filter', 'none')
      ]
    );
  }

  #[Route(path: "/user/edit/{id}", name: "user_edit")]
  public function edit(UserRepository $userRepository, int $id)
  {
    $user = $userRepository->find($id);
    var_dump($user);
  }
}
