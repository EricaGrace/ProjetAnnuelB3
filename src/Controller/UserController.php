<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Routing\Attribute\Route;
use App\Session\SessionInterface;
use DateTime;

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
        dump($user);
    }

    #[Route(path: "/user/add", name: "add_user")]
    public function addUser(UserRepository $userRepository)
    {
        $user = new User();

        $user->setName("Bob")
            ->setFirstName("John")
            ->setUsername("Bobby")
            ->setPassword("randompass")
            ->setEmail("bob@bob.com")
            ->setBirthDate(new DateTime('now'));

        $userRepository->save($user);

        echo "User saved";
    }
}
