<?php

namespace App\Controller;

use App\Repository\TasklistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TasklistController extends AbstractController
{
    /**
     * @Route("/", name="tasklists")
     */
    public function index(TasklistRepository $tasklistRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $tasklists = $tasklistRepository->findUsersActiveLists($this->getUser());

        return $this->render('tasklist/index.html.twig', [
            'tasklists' => $tasklists,
        ]);
    }

    /**
     * @Route("/archives", name="archived_tasklists")
     */
    public function getArchivedLists(TasklistRepository $tasklistRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $tasklists = $tasklistRepository->findUsersArchivedLists($this->getUser());

        return $this->render('tasklist/archivedLists.html.twig', [
            'tasklists' => $tasklists,
        ]);
    }
}
