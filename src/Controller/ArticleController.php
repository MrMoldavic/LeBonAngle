<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\DefinitionRepository;
use App\Form\EntityType;

class ArticleController extends AbstractController
{
    #[IsGranted('ROLE_USER', message: 'Seuls les Users peuvent faire ça !')]
    #[Route('/article_create', name: 'article_create')]
    public function article_create(Request $request, ManagerRegistry $doctrine): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $article->setTitle($form->get('title')->getData());
            $article->setPrice($form->get('price')->getData());
            $article->setDescription($form->get('description')->getData());
            $article->setCategory($form->get('category')->getData());
            $article->setCreatedAt(new \DateTimeImmutable('now'));
            
            $em = $doctrine->getManager();
            $em->persist($article);
            $em->flush();

            $this->addFlash('success', "Catégorie créée avec succès!");
            return $this->redirectToRoute('edit_article', ['id' => $article->getId()]);
        }

        return $this->renderForm('article/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/article/{id}', name: 'article')]
    public function showArticle(EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Category::class)->find($id);

        if (!$article) {
            // Reroute to index or categories list
        } else {
            return $this->render('category/index.html.twig', [
                'categories' => $article,
            ]);
        }
    }

    #[Route('/article_edit/{id}', name: 'edit_article')]
    public function editArticle(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($id);

        // If no category, redirect to list of categories
        if (!$article) {
            return $this->redirectToRoute('articles');
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();

            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('articles');
        }

        return $this->renderForm('article/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/articles', name: 'articles')]
    #[Security("is_granted('ROLE_USER')", statusCode: 403, message: "Resource not found.")]
    public function allArticles(EntityManagerInterface $entityManager): Response
    {
        $articles = $entityManager->getRepository(Article::class)->findAll();

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
