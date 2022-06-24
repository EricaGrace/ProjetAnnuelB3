<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Routing\Attribute\Route;
use App\Routing\Router;
use DateTime;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SignupController extends AbstractController
{
 

    #[Route(path: "/signup", name: "signup", httpMethod: 'POST')]
    public function signUp (UserRepository $userRepository, Request $request, Router $router)
    {
       /* It's getting the request from the form. */
        $form = $request->request;
        
       /* It's checking if the form is valid. */
        try {
            Validator::alpha()->assert($form->get('last_name'));
            Validator::alpha()->assert($form->get('first_name'));
            Validator::alnum()->assert($form->get('username'));
            Validator::alnum()->assert($form->get('password'));
            Validator::email()->assert($form->get('email'));
            Validator::Phone()->assert($form->get('phone'));
            Validator::date('d/m/Y')->assert($form->get('birthdate'));
        } catch(NestedValidationException $exception) {
            return $this->render('User/inscription.html.twig',  [
                'messages' => $exception->getMessages(),
                'old' => $form
            ]);

        }       
      
        $user = new User();

        $birthDate = DateTime::createFromFormat('d/m/Y', $form->get('birthdate'));

       /* It's setting the user's information. */
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

       /* It's redirecting the user to the login page. */
        return new RedirectResponse($router->getRouteUriFromName('login')); 
    }

    #[Route(path: "/signup", name: "signup", httpMethod: 'GET')]
    public function index ()
    {
       /* It's rendering the signup page. */
        return $this->render(
            'user/inscription.html.twig',
        );
    }
}