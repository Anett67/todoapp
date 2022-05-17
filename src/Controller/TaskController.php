<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Entity\Tasklist;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    /**
     * @Route("/task", name="app_task")
     */
    public function index(): Response
    {
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }

    /**
     * @Route("/task/{id}/creation", name="create_task")
     */
    public function tasklistCreateOrEdit(Tasklist $tasklist = null, Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $task->setTasklist($tasklist);
            $task->setCreatedAt();
            $task->setUpdatedAt();
            $manager->persist($task);
            $manager->flush();

            $this->addFlash('success', 'La tâche a été ajoutée avec succès');

            return $this->redirectToRoute('tasklist', ['id' => $tasklist->getId()]);
        }

        return $this->render('tasklist/createEditTasklist.html.twig', [
            'form' => $form->createView(),
            'meta_title' => 'Ajouter une liste de tâche',
            'title' => 'Ajouter une liste de tâche'
        ]);
    }

}
