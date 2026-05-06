<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/categories', name: 'api_categories_')]
class CategoryController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(CategoryRepository $repo): JsonResponse
    {
        $categories = $repo->findAll();

        $data = array_map(fn(Category $c) => [
            'id' => $c->getId(),
            'name' => $c->getName(),
        ], $categories);

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, CategoryRepository $repo): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'])) {
            return $this->json(['error' => 'Le nom est obligatoire'], 400);
        }

        $existing = $repo->findOneBy(['name' => $data['name']]);
        if ($existing) {
            return $this->json(['error' => 'Cette categorie existe deja'], 409);
        }

        $category = new Category();
        $category->setName($data['name']);

        $em->persist($category);
        $em->flush();

        return $this->json(['message' => 'Categorie creee', 'id' => $category->getId()], 201);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Category $category, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($category);
        $em->flush();

        return $this->json(['message' => 'Categorie supprimee']);
    }
}
