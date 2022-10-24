<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Form\UserFormType;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class UserController extends AbstractController
{
    #[Route('/profil', name: 'profil')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setUpdatedAt(new \DateTimeImmutable('now'));

            $em = $doctrine->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "Utilisateur modifié avec succès!");
            return $this->redirectToRoute('profil');
        }

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}
