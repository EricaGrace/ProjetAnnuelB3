<?php

namespace App\Controller;

use App\Auth\Authenticator;
use App\Entity\Event;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Venue;
use App\Repository\EventCategoryRepository;
use App\Repository\EventRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Routing\Attribute\Route;
use App\Utils\Config;
use DateTime;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    #[Route('/admin/event/add', name: 'event.add')]
    public function addEvent(EventCategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll();
        return $this->render('Administration/AjouterEvenement.html.twig', [
            'categories' => $categories
        ]);
    }

    // TODO: refactoriser
    #[Route('/admin/event/add', httpMethod: 'POST')]
    public function storeEvent(Request $request, Config $config, EventRepository $eventRepository, EventCategoryRepository $categoryRepository, Authenticator $auth)
    {
        $post = $request->request;
        $image = $request->files->getIterator()->current();

        $event = $eventRepository->findBySlug($post->get('slug'));
        $category = $categoryRepository->find($post->get('category'));

        try {
            Validator::alpha()->assert($title = $post->get('title'));
            Validator::slug()->assert($slug = $post->get('slug'));
            Validator::nullType()->assert($event);
            Validator::trueVal()->assert((bool)$category);
            Validator::date()->assert($date = $post->get('date'));
            Validator::numericVal()->min(0)->assert($price = $post->get('price'));
            Validator::intVal()->min(0)->assert($maxAttendees = $post->get('maxAttendees'));
            Validator::image()->validate($image->getClientOriginalName());
            Validator::stringType()->validate($description = $post->get('description'));
        } catch (NestedValidationException $exception) {
            return $this->render('Administration/AjouterEvenement.html.twig', [
                'messages' => $exception->getMessages([
                    'nullType' => 'Un évènement existe déjà avec ce slug',
                    'category' => 'La catégorie sélectionnée n\'existe pas'
                ]),
                'categories' => $categoryRepository->findAll(),
                'old' => $post
            ]);
        }

        $uploadFolderPath = $config('uploads')['images'];
        $image = $image->move(dirname(__DIR__, 2) . "public/$uploadFolderPath", $image->getFileName() . '.' . $image->getClientOriginalExtension());
        $imagePathname = $uploadFolderPath . $image->getFilename();

        $event = (new Event())
            ->setTitle($title)
            ->setCategory($category)
            ->setSlug($slug)
            ->setDate(new DateTime($date))
            ->setMaxAttendees($maxAttendees)
            ->setPrice($price)
            ->setCreator($auth->getAuthenticatedUser() ?: (new User())->setId(1))
            ->setDescription($description)
            ->setImage($imagePathname)
            ->setVenue((new Venue())->setId(1));

        $eventRepository->save($event);

        return $this->render('Administration/AjouterEvenement.html.twig', [
            'messages' => ['Evenement enregistré avec succès'],
            'categories' => $categoryRepository->findAll(),
            'old' => $post
        ]);
    }

    #[Route('/admin/user/add', name: 'user.add')]
    public function addUser(RoleRepository $roleRepository)
    {
        $roles = $roleRepository->findAll();
        return $this->render('Administration/AjouterUtilisateur.html.twig', [
            'action' => 'Ajouter',
            "roles" => $roles
        ]);
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

        return $this->render('Administration/AjouterUtilisateur.html.twig', [
            'action' => 'Ajouter',
            'messages' => ['Utilisateur ajouté']
        ]);
    }

    #[Route(path: "/admin/user/edit/{id}", name: "user.edit")]
    public function edit(UserRepository $userRepository, string $id)
    {
        $user = $userRepository->find($id);

        return $this->renderIf('Administration/AjouterUtilisateur.html.twig', [
            'user' => $user,
            'action' => 'Modifier'
        ], $user);
    }
}