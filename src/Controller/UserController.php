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

        echo $this->render(
            'user/list.html.twig',
            [
                'users' => $users,
                'filter' => $session->get('filter', 'none')
            ]
        );
    }

    #[Route(path: "/user/edit/{username}", name: "user_edit")]
    public function edit(UserRepository $userRepository, string $username)
    {
        $user = $userRepository->findByUsername($username);
        dump($user);
    }
}
