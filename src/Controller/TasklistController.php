<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TasklistController extends AbstractController
{
    /**
     * @Route("/", name="tasklists")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('tasklist/index.html.twig', [
            'controller_name' => 'TasklistController',
        ]);
    }
}
