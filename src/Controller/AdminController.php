<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventCategory;
use App\Entity\User;
use App\Repository\EventRepository;
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
        $form = $request->request;
        $image = $request->files->getIterator()->current();

        try {
            Validator::alpha()->assert($title = $form->get('title'));
            Validator::slug()->assert($slug = $form->get('slug'));
            Validator::trueVal()->assert((bool)$eventRepository->findBySlug($form->get('slug')));
            Validator::date()->assert($date = $form->get('date'));
            Validator::numericVal()->min(0)->assert($price = $form->get('price'));
            Validator::intVal()->min(0)->assert($maxAttendees = $form->get('maxAttendees'));
            Validator::image()->validate($image->getClientOriginalName());
            Validator::stringType()->validate($description = $form->get('description'));

        } catch (NestedValidationException $exception) {
            return $this->render('Administration/AjouterEvenement.html.twig', [
                'messages' => $exception->getMessages(),
                'old' => $form
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
}