<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;

class BaseController extends AbstractController
{


    #[Route('/', name: 'home')]
    public function home(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();

        return $this->render('home/home.html.twig', [
            'categories' => $categories,
        ]);
    }


    // public function header(CategoryRepository $categoryRepository, $routeName, Request $request )
    // {
    //     $categories = $categoryRepository->findAll();
    //     return $this->render('base/header.html.twig', [
    //         'route_name' => $routeName,
    //         'categories' => $categories,
    //     ]);
    // }


    #[Route('/redirect-user', name: 'redirect_user')]
    public function redirectUser()
    {
        return $this->redirectToRoute('home');
        // if ($this->isGranted('ROLE_ADMIN')) {
        //     return $this->redirectToRoute('home');
        // }
        // else {
        //     return $this->redirectToRoute('login');
        // }
    }

}
