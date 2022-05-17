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
    public function taskCreate(Tasklist $tasklist, Request $request, EntityManagerInterface $manager): Response
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

        return $this->render('task/createEditTask.html.twig', [
            'form' => $form->createView(),
            'meta_title' => 'Ajouter une tâche',
            'title' => 'Ajouter une tâche'
        ]);
    }

    /**
     * @Route("/task/{id}/edition", name="edit_task")
     */
    public function taskEdit(Task $task, Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $task->setUpdatedAt();
            $manager->persist($task);
            $manager->flush();

            $this->addFlash('success', 'La tâche a été modifiée avec succès');

            return $this->redirectToRoute('tasklist', ['id' => $task->getTasklist()->getId()]);
        }

        return $this->render('task/createEditTask.html.twig', [
            'form' => $form->createView(),
            'meta_title' => 'Modifier la tâche',
            'title' => "Modifier la tâche '" . $task->getTitle() . "'"
        ]);
    }

    /**
     * @Route("/task/{id}/complete", name="complete_task", requirements={"id":"\d+"})
     */
    public function completeTask(Task $task, EntityManagerInterface $manager, Request $request): Response
    {
        if($this->isCsrfTokenValid('COMP' . $task->getId(), $request->get('_token'))){
            $task->setCompleted(true);
            $task->setUpdatedAt();
            $manager->persist($task);
            $manager->flush();
        }
        
        return $this->redirectToRoute('tasklist', ['id' => $task->getTasklist()->getId()]);
    }

    /**
     * @Route("/task/{id}/activate", name="activate_task", requirements={"id":"\d+"})
     */
    public function activateTask(Task $task, EntityManagerInterface $manager, Request $request): Response
    {
        if($this->isCsrfTokenValid('REACT' . $task->getId(), $request->get('_token'))){
            $task->setCompleted(false);
            $task->setUpdatedAt();
            $manager->persist($task);
            $manager->flush();
        }
        
        return $this->redirectToRoute('tasklist', ['id' => $task->getTasklist()->getId()]);
    }

    /**
     * @Route("/task/{id}/delete", name="delete_task", requirements={"id":"\d+"})
     */
    public function deleteTask(Task $task, EntityManagerInterface $manager, Request $request): Response
    {
        if($this->isCsrfTokenValid('SUP' . $task->getId(), $request->get('_token'))){
            $manager->remove($task);
            $manager->flush();
            $this->addFlash("success",  "La tâche a bien été supprimé");
        }
        
        return $this->redirectToRoute('tasklist', ['id' => $task->getTasklist()->getId()]);
    }
}
