<?php

namespace App\Controller;

use App\Entity\Tasklist;
use App\Form\TasklistType;
use App\Repository\TasklistRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/tasklist/{id}", name="tasklist", requirements={"id":"\d+"})
     */
    public function tasklist(Tasklist $tasklist): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('tasklist/tasklist.html.twig', [
            'list' => $tasklist,
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

    /**
     * @Route("/tasklist/creation", name="create_tasklists")
     * @Route("/tasklist/{id}/edition", name="edit_tasklists", requirements={"id":"\d+"})
     */
    public function tasklistCreateOrEdit(Tasklist $tasklist = null, Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 
        $tasklist = $tasklist ?? new Tasklist();
        $edition = $tasklist->getId();

        $form = $this->createForm(TasklistType::class, $tasklist);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $tasklist->setUser($this->getUser());
            if(!$edition) $tasklist->setCreatedAt();
            $tasklist->setUpdatedAt();
            $manager->persist($tasklist);
            $manager->flush();

            $this->addFlash('success', $edition ? 'La liste a été renommée avec succès' : 'La liste a été ajoutée avec succès');

            if($edition) {
                return $this->redirectToRoute('tasklist', ['id' => $tasklist->getId()]);
            }

            return $this->redirectToRoute('tasklists');
        }

        return $this->render('tasklist/createEditTasklist.html.twig', [
            'form' => $form->createView(),
            'meta_title' => $edition ? 'Renommer la liste' : 'Ajouter une liste de tâche',
            'title' => $edition ? "Renommer la liste '" . $tasklist->getTitle() . "'" : 'Ajouter une liste de tâche'
        ]);
    }

    /**
     * @Route("/tasklist/{id}/archive", name="archive_tasklist", requirements={"id":"\d+"})
     */
    public function archiveList(Tasklist $tasklist, EntityManagerInterface $manager, Request $request): Response
    {
        if($this->isCsrfTokenValid('ARC' . $tasklist->getId(), $request->get('_token'))){
            $tasklist->setArchivedAt(new DateTimeImmutable());
            $tasklist->setUpdatedAt();
            $manager->persist($tasklist);
            $manager->flush();
            $this->addFlash("success",  "La liste a bien été archivée");
        }
        
        return $this->redirectToRoute('tasklists');
    }
    
    /**
     * @Route("/tasklist/{id}/activate", name="activate_tasklist", requirements={"id":"\d+"})
     */
    public function activateList(Tasklist $tasklist, EntityManagerInterface $manager, Request $request): Response
    {
        if($this->isCsrfTokenValid('ACT' . $tasklist->getId(), $request->get('_token'))){
            $tasklist->setArchivedAt();
            $tasklist->setUpdatedAt();
            $manager->persist($tasklist);
            $manager->flush();
            $this->addFlash("success",  "La liste a bien été activée");
        }
        
        return $this->redirectToRoute('tasklists');
    }

    /**
     * @Route("/tasklist/{id}/delete", name="delete_tasklist", requirements={"id":"\d+"})
     */
    public function deleteList(Tasklist $tasklist, EntityManagerInterface $manager, Request $request): Response
    {
        if($this->isCsrfTokenValid('SUP' . $tasklist->getId(), $request->get('_token'))){
            $manager->remove($tasklist);
            $manager->flush();
            $this->addFlash("success",  "La liste a bien été supprimé");
        }
        
        return $this->redirectToRoute('tasklists');
    }
}
