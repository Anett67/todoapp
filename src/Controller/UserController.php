<?php

namespace App\Controller;

use App\Form\UserThemeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/theme", name="change_theme")
     */
    public function index(EntityManagerInterface $manager, Request $request): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserThemeType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('change_theme');
        }

        return $this->render('user/changeTheme.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
