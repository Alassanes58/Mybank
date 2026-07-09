<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class CategoryController extends AbstractController
{
    #[Route('/api/categories', name: 'api_categories_list', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): JsonResponse
    {
        $categories = $categoryRepository->findAll();

        $data = [];

        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'title' => $category->getTitle(),
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/categories', name: 'api_categories_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $category = new Category();
        $category->setTitle($data['title']);

        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json([
            'message' => 'Catégorie créée avec succès',
            'id' => $category->getId(),
            'title' => $category->getTitle(),
        ], Response::HTTP_CREATED);
    }
    #[Route('/api/categories/{id}', name: 'api_categories_update', methods: ['PUT'])]
public function update(
    int $id,
    Request $request,
    CategoryRepository $categoryRepository,
    EntityManagerInterface $entityManager
): JsonResponse
{
    $category = $categoryRepository->find($id);

    if (!$category) {
        throw new NotFoundHttpException('Catégorie introuvable');
    }

    $data = json_decode($request->getContent(), true);

    $category->setTitle($data['title']);

    $entityManager->flush();

    return $this->json([
        'message' => 'Catégorie modifiée avec succès',
        'id' => $category->getId(),
        'title' => $category->getTitle(),
    ]);
}
     #[Route('/api/categories/{id}', name: 'api_categories_delete', methods: ['DELETE'])]
public function delete(
    int $id,
    CategoryRepository $categoryRepository,
    EntityManagerInterface $entityManager
): JsonResponse
{
    $category = $categoryRepository->find($id);

    if (!$category) {
        throw new NotFoundHttpException('Catégorie introuvable');
    }

    $entityManager->remove($category);
    $entityManager->flush();

    return $this->json([
        'message' => 'Catégorie supprimée avec succès'
    ]);
}
}
