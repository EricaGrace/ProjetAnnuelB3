<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Routing\Attribute\Route;
use DateTime;
use Respect\Validation\Validator;
use Respect\Validation\Exceptions\NestedValidationException;
use Symfony\Component\HttpFoundation\Request;

class SignupController extends AbstractController
{
 

    #[Route(path: "/signup", name: "signup", httpMethod: 'POST')]
    public function signUp (UserRepository $userRepository, Request $request )
    {
        $form = $request->request;

        
        try {
            Validator::alpha('')->assert($form->get('last_name'));
            Validator::alpha('')->assert($form->get('first_name'));
            Validator::alnum()->assert($form->get('username'));
            Validator::alnum()->assert($form->get('password'));
            Validator::email()->assert($form->get('email'));
            Validator::Phone()->assert($form->get('phone'));
            Validator::date('d/m/Y')->assert($form->get('birthdate'));
        } catch(NestedValidationException $exception) {
            print_r(
                $exception->getMessages([
                    'alnum' => '{{name}} must contain only letters and digits',
                    'noWhitespace' => '{{name}} cannot contain spaces',
                    'length' => '{{name}}    must not have more than 15 chars',
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

    #[Route(path: "/signup", name: "signup", httpMethod: 'GET')]
    public function index ()
    {
        echo $this->twig->render(
            'user/inscription.html.twig',
        );
    }
}