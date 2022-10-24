<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CategoryType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EntityType;


class CategoryController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN', message: 'Seuls les Admins peuvent faire ça !')]
    #[Route('/category_create', name: 'category_create')]
    public function category_create(Request $request, ManagerRegistry $doctrine): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $category->setName($form->get('name')->getData());
            $em = $doctrine->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash('success', "Catégorie créée avec succès!");
            return $this->redirectToRoute('app_category', ['id' => $category->getId()]);
        }

        return $this->renderForm('category/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/categories', name: 'categories')]
    #[Security("is_granted('ROLE_ADMIN')", statusCode: 403, message: "Resource not found.")]
    public function allCategories(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'app_category')]
    public function showCategory(EntityManagerInterface $entityManager, int $id): Response
    {
        $category = $entityManager->getRepository(Category::class)->find($id);

        if (!$category) {
            // Reroute to index or categories list
        } else {
            return $this->render('category/index.html.twig', [
                'categories' => $category,
            ]);
        }
    }

    #[Route('/category_edit/{id}', name: 'edit_category')]
    public function editCategory(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $category = $entityManager->getRepository(Category::class)->find($id);

        // If no category, redirect to list of categories
        if (!$category) {
            return $this->redirectToRoute('categories');
        }

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('categories');
        }

        return $this->renderForm('category/create.html.twig', [
            'form' => $form,
        ]);
    }
}
