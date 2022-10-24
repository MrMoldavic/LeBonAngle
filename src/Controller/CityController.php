<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\City;
use App\Form\CityType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class CityController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN', message: 'Seuls les Admins peuvent faire ça !')]
    #[Route('/city_create', name: 'city_create')]
    public function city_create(Request $request, ManagerRegistry $doctrine): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $city->setName($form->get('name')->getData());
            $em = $doctrine->getManager();
            $em->persist($city);
            $em->flush();

            $this->addFlash('success', "Catégorie créée avec succès!");
            return $this->redirectToRoute('edit_city', ['id' => $city->getId()]);
        }

        return $this->renderForm('city/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/city/{id}', name: 'city')]
    public function showCity(EntityManagerInterface $entityManager, int $id): Response
    {
        $cities = $entityManager->getRepository(city::class)->find($id);

        if (!$cities) {
            // Reroute to index or categories list
        } else {
            return $this->render('city/index.html.twig', [
                'cities' => $cities,
            ]);
        }
    }

    #[Route('/cities', name: 'cities')]
    #[Security("is_granted('ROLE_ADMIN')", statusCode: 403, message: "Resource not found.")]
    public function allCategories(EntityManagerInterface $entityManager): Response
    {
        $cities = $entityManager->getRepository(City::class)->findAll();

        return $this->render('city/index.html.twig', [
            'cities' => $cities,
        ]);
    }

    #[Route('/city_edit/{id}', name: 'edit_city')]
    public function editCity(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $city = $entityManager->getRepository(City::class)->find($id);

        // If no category, redirect to list of categories
        if (!$city) {
            return $this->redirectToRoute('cities');
        }

        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->getData();

            $entityManager->persist($city);
            $entityManager->flush();

            $this->addFlash('success', "Villes modifiée avec succès!");
            return $this->redirectToRoute('cities');
        }

        return $this->renderForm('city/create.html.twig', [
            'form' => $form,
        ]);
    }
}
