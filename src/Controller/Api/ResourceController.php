<?php

namespace App\Controller\Api;

use App\Entity\Resource;
use App\Repository\ResourceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/resources', name: 'api_resources_')]
class ResourceController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ResourceRepository $repo, Request $request): JsonResponse
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $type = $request->query->get('type');
        $playlistId = $request->query->getInt('playlist');

        $qb = $repo->createQueryBuilder('r')
            ->leftJoin('r.playlist', 'p')
            ->addSelect('p')
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        if ($type) {
            $qb->andWhere('r.type = :type')->setParameter('type', $type);
        }

        if ($playlistId) {
            $qb->andWhere('r.playlist = :playlist')->setParameter('playlist', $playlistId);
        }

        $resources = $qb->getQuery()->getResult();

        $data = array_map(fn(Resource $r) => [
            'id' => $r->getId(),
            'title' => $r->getTitle(),
            'type' => $r->getType(),
            'description' => $r->getDescription(),
            'url' => $r->getUrl(),
            'coverImage' => $r->getCoverImage(),
            'publishedAt' => $r->getPublishedAt()?->format('Y-m-d'),
            'isAvailable' => $r->isAvailable(),
            'playlist' => $r->getPlaylist() ? [
                'id' => $r->getPlaylist()->getId(),
                'name' => $r->getPlaylist()->getName(),
            ] : null,
        ], $resources);

        return $this->json(['data' => $data, 'page' => $page]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Resource $resource): JsonResponse
    {
        return $this->json([
            'id' => $resource->getId(),
            'title' => $resource->getTitle(),
            'type' => $resource->getType(),
            'description' => $resource->getDescription(),
            'url' => $resource->getUrl(),
            'coverImage' => $resource->getCoverImage(),
            'publishedAt' => $resource->getPublishedAt()?->format('Y-m-d'),
            'isAvailable' => $resource->isAvailable(),
            'playlist' => $resource->getPlaylist() ? [
                'id' => $resource->getPlaylist()->getId(),
                'name' => $resource->getPlaylist()->getName(),
            ] : null,
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        if (!isset($data['title'], $data['type'], $data['publishedAt'])) {
            return $this->json(['error' => 'Champs manquants'], 400);
        }

        $resource = new Resource();
        $resource->setTitle($data['title']);
        $resource->setType($data['type']);
        $resource->setDescription($data['description'] ?? null);
        $resource->setUrl($data['url'] ?? null);
        $resource->setPublishedAt(new \DateTime($data['publishedAt']));
        $resource->setIsAvailable(true);
        $resource->setCreatedAt(new \DateTimeImmutable());

        if (!empty($data['playlistId'])) {
            $playlist = $em->getRepository(\App\Entity\Playlist::class)->find($data['playlistId']);
            if ($playlist) {
                $resource->setPlaylist($playlist);
            }
        }

        $em->persist($resource);
        $em->flush();

        return $this->json(['message' => 'Ressource creee', 'id' => $resource->getId()], 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Resource $resource, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) $resource->setTitle($data['title']);
        if (isset($data['type'])) $resource->setType($data['type']);
        if (isset($data['description'])) $resource->setDescription($data['description']);
        if (isset($data['url'])) $resource->setUrl($data['url']);
        if (isset($data['publishedAt'])) $resource->setPublishedAt(new \DateTime($data['publishedAt']));
        if (isset($data['isAvailable'])) $resource->setIsAvailable($data['isAvailable']);

        $em->flush();

        return $this->json(['message' => 'Ressource mise a jour']);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Resource $resource, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em->remove($resource);
        $em->flush();

        return $this->json(['message' => 'Ressource supprimee']);
    }
}
