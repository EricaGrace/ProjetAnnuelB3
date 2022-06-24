<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\Role;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use App\Routing\Attribute\Route;
use App\Utils\Config;
use DateTime;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    #[Route('/admin/event/add', name: 'event.add')]
    public function addEvent()
    {
        return $this->render('Administration/AjouterEvenement.html.twig');
    }

    // TODO: refactoriser
    #[Route('/admin/event/add', httpMethod: 'POST')]
    public function storeEvent(Request $request, Config $config, EventRepository $eventRepository)
    {
        $post = $request->request;
        $image = $request->files->getIterator()->current();

        try {
            Validator::alpha()->assert($title = $post->get('title'));
            Validator::slug()->assert($slug = $post->get('slug'));
            Validator::trueVal()->assert((bool)$eventRepository->findBySlug($post->get('slug')));
            Validator::date()->assert($date = $post->get('date'));
            Validator::numericVal()->min(0)->assert($price = $post->get('price'));
            Validator::intVal()->min(0)->assert($maxAttendees = $post->get('maxAttendees'));
            Validator::image()->validate($image->getClientOriginalName());
            Validator::stringType()->validate($description = $post->get('description'));
        } catch (NestedValidationException $exception) {
            return $this->render('Administration/AjouterEvenement.html.twig', [
                'messages' => $exception->getMessages(),
                'old' => $post
            ]);
        }

        $uploadFolderPath = $config('uploads')['images'];
        $image = $image->move(dirname(__DIR__, 2) . "public/$uploadFolderPath", $image->getFileName() . '.' . $image->getClientOriginalExtension());
        $imagePathname = $uploadFolderPath . $image->getFilename();

        $event = (new Event())
            ->setTitle($title)
            ->setCategory((new EventCategory())->setId(1))
            ->setSlug($slug)
            ->setDate(new DateTime($date))
            ->setMaxAttendees($maxAttendees)
            ->setPrice($price)
            ->setCreator((new User())->setId(1))
            ->setDescription($description)
            ->setImage($imagePathname);

        $eventRepository->save($event);

        return $this->render('Administration/AjouterEvenement.html.twig');
    }

    #[Route('/admin/user/add', name: 'user.add')]
    public function addUser()
    {
        return $this->render('Administration/AjouterUtilisateur.html.twig');
    }

    #[Route('/admin/user/add', httpMethod: 'POST')]
    public function storeUser(Request $request, UserRepository $userRepository)
    {
        $post = $request->request;

        try {
            Validator::alpha()->assert($post->get('last_name'));
            Validator::alpha()->assert($post->get('first_name'));
            Validator::alnum()->assert($post->get('username'));
            Validator::alnum()->assert($post->get('password'));
            Validator::email()->assert($post->get('email'));
            Validator::optional(Validator::phone())->assert($post->get('phone'));
            Validator::optional(Validator::date('d/m/Y'))->assert($post->get('birthdate'));
        } catch (NestedValidationException $exception) {
            return $this->render('Administration/AjouterUtilisateur.html.twig', [
                'messages' => $exception->getMessages(),
                'old' => $post
            ]);
        }

        $birthDate = DateTime::createFromFormat('d/m/Y', $post->get('birthdate'));

        $user = (new User())->setName($post->get('last_name'))
            ->setFirstName($post->get('first_name'))
            ->setUsername($post->get('username'))
            ->setPassword($post->get('password'))
            ->setEmail($post->get('email'))
            ->setPhone($post->get('phone'))
            ->setRole((new Role())->setID(1)->setName('user'))
            ->setCreatedAt(new DateTime())
            ->setBirthDate($birthDate);

        $userRepository->save($user);

        return $this->render('Administration/AjouterUtilisateur.html.twig');
    }
}