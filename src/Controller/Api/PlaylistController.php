<?php

namespace App\Controller\Api;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/playlists', name: 'api_playlists_')]
class PlaylistController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(PlaylistRepository $repo): JsonResponse
    {
        $playlists = $repo->findAll();

        $data = array_map(fn(Playlist $p) => [
            'id' => $p->getId(),
            'name' => $p->getName(),
            'description' => $p->getDescription(),
            'createdAt' => $p->getCreatedAt()?->format('Y-m-d'),
            'resourceCount' => $p->getResources()->count(),
        ], $playlists);

        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Playlist $playlist): JsonResponse
    {
        return $this->json([
            'id' => $playlist->getId(),
            'name' => $playlist->getName(),
            'description' => $playlist->getDescription(),
            'createdAt' => $playlist->getCreatedAt()?->format('Y-m-d'),
            'resourceCount' => $playlist->getResources()->count(),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'])) {
            return $this->json(['error' => 'Le nom est obligatoire'], 400);
        }

        $playlist = new Playlist();
        $playlist->setName($data['name']);
        $playlist->setDescription($data['description'] ?? null);
        $playlist->setCreatedAt(new \DateTimeImmutable());

        $em->persist($playlist);
        $em->flush();

        return $this->json(['message' => 'Playlist creee', 'id' => $playlist->getId()], 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Playlist $playlist, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) $playlist->setName($data['name']);
        if (isset($data['description'])) $playlist->setDescription($data['description']);

        $em->flush();

        return $this->json(['message' => 'Playlist mise a jour']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Playlist $playlist, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($playlist->getResources()->count() > 0) {
            return $this->json(['error' => 'Impossible de supprimer une playlist contenant des ressources'], 400);
        }

        $em->remove($playlist);
        $em->flush();

        return $this->json(['message' => 'Playlist supprimee']);
    }
}
