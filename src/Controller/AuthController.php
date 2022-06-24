<?php

namespace App\Controller;

use App\Auth\Authenticator;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Routing\Attribute\Route;
use DateTime;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends AbstractController
{


    #[Route(path: "/signup", httpMethod: 'POST', name: "signup")]
    public function signUp(UserRepository $userRepository, Request $request)
    {
        $form = $request->request;


        try {
            Validator::alpha('')->assert($form->get('last_name'));
            Validator::alpha('')->assert($form->get('first_name'));
            Validator::alnum()->assert($form->get('username'));
            Validator::alnum()->assert($form->get('password'));
            Validator::email()->assert($form->get('email'));
            Validator::falseVal()->assert((bool)$userRepository->findByEmail($form->get('email')));
            Validator::Phone()->assert($form->get('phone'));
            Validator::date('d/m/Y')->assert($form->get('birthdate'));
        } catch (NestedValidationException $exception) {
            print_r(
                $exception->getMessages([
                    'falseVal' => "Un utilisateur avec cette adresse existe déjà."
                ])
            );
        }

        $user = new User();

        $birthDate = DateTime::createFromFormat('d/m/Y', $form->get('birthdate'));

        $user->setName($form->get('last_name'))
            ->setFirstName($form->get('first_name'))
            ->setUsername($form->get('username'))
            ->setPassword($form->get('password'))
            ->setEmail($form->get('email'))
            ->setPhone($form->get('phone'))
            ->setRole((new Role())->setID(1)->setName('user'))
            ->setCreatedAt(new DateTime())
            ->setBirthDate($birthDate);

        $userRepository->save($user);

        echo "User saved";
    }

    #[Route(path: "/signup", httpMethod: 'GET', name: "signup")]
    public function signupPage()
    {
        echo $this->twig->render(
            'user/inscription.html.twig',
        );
    }

    #[Route(path: "/login", httpMethod: 'GET', name: "login")]
    public function loginPage(Authenticator $authenticator)
    {
        // TODO: extract to middleware
        if ($authenticator->isAuthenticated()) {
            return new RedirectResponse($this->router->route('home'));
        }

        return $this->render('User/login.html.twig');
    }

    #[Route(path: "/login", httpMethod: 'POST')]
    public function login(Request $request, UserRepository $userRepository, Authenticator $authenticator)
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $user = $userRepository->findByEmail($email);

        try {
            Validator::email()->assert($email);
            Validator::trueVal()->assert((bool)$user);
            Validator::alnum('%&-_')->assert($password);
            Validator::equals($user->getPassword())->assert(password_verify($password, $user->getPassword()));
        } catch (NestedValidationException $e) {
            return $this->render('User/login.html.twig', [
                'messages' => $e->getMessages([
                    'equals' => 'Le mot de passe est incorrect'
                ]),
                'old' => $request->request
            ]);
        }

        $authenticator->authenticate($user->getId());
        return new RedirectResponse($this->router->route('user.account'));
    }

    #[Route(path: "/logout", httpMethod: 'GET', name: 'logout')]
    public function logout(Authenticator $authenticator)
    {
        if ($authenticator->isAuthenticated()) {
            $authenticator->logout();
        }

        return new RedirectResponse($this->router->route('home'));
    }
}