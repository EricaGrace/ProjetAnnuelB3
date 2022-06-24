<?php

namespace App\Controller;

use App\Auth\Authenticator;
use App\Repository\UserRepository;
use App\Routing\Attribute\Route;
use App\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UserController extends AbstractController
{
    #[Route(path: "/users", name: "users.list")]
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

    #[Route(path: "/compte", httpMethod: "GET", name: "user.account")]
    public function account(Authenticator $authenticator)
    {
        if (!$authenticator->isAuthenticated()) {
            return new RedirectResponse($this->router->route('login'));
        }

        $user = $authenticator->getAuthenticatedUser();
        return $this->renderIf('User/MonCompte.html.twig', [
            'user' => $user
        ], $user);
    }
}
