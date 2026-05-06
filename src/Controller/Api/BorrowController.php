<?php

namespace App\Controller\Api;

use App\Entity\Borrow;
use App\Repository\BorrowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/borrows', name: 'api_borrows_')]
class BorrowController extends AbstractController
{
    #[Route('/mine', name: 'mine', methods: ['GET'])]
    public function mine(BorrowRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        $borrows = $repo->findBy(['user' => $user]);

        $data = array_map(fn(Borrow $b) => [
            'id' => $b->getId(),
            'resource' => [
                'id' => $b->getResource()->getId(),
                'title' => $b->getResource()->getTitle(),
                'type' => $b->getResource()->getType(),
            ],
            'borrowedAt' => $b->getBorrowedAt()?->format('Y-m-d H:i'),
            'dueAt' => $b->getDueAt()?->format('Y-m-d'),
            'returnedAt' => $b->getReturnedAt()?->format('Y-m-d H:i'),
            'status' => $b->getStatus(),
        ], $borrows);

        return $this->json($data);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, BorrowRepository $repo): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (!isset($data['resourceId'])) {
            return $this->json(['error' => 'resourceId manquant'], 400);
        }

        $resource = $em->getRepository(\App\Entity\Resource::class)->find($data['resourceId']);
        if (!$resource) {
            return $this->json(['error' => 'Ressource introuvable'], 404);
        }

        if (!$resource->isAvailable()) {
            return $this->json(['error' => 'Ressource non disponible'], 400);
        }

        $activeBorrows = $repo->findBy(['user' => $user, 'status' => 'EN_COURS']);
        if (count($activeBorrows) >= 3) {
            return $this->json(['error' => 'Limite de 3 emprunts simultanees atteinte'], 400);
        }

        $borrow = new Borrow();
        $borrow->setUser($user);
        $borrow->setResource($resource);
        $borrow->setBorrowedAt(new \DateTimeImmutable());
        $borrow->setDueAt(new \DateTime('+14 days'));
        $borrow->setStatus('EN_COURS');

        $resource->setIsAvailable(false);

        $em->persist($borrow);
        $em->flush();

        return $this->json([
            'message' => 'Emprunt enregistre',
            'id' => $borrow->getId(),
            'dueAt' => $borrow->getDueAt()->format('Y-m-d'),
        ], 201);
    }

    #[Route('/{id}/return', name: 'return', methods: ['PUT'])]
    public function return(Borrow $borrow, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        if ($borrow->getUser() !== $user) {
            return $this->json(['error' => 'Acces refuse'], 403);
        }

        if ($borrow->getStatus() === 'RETOURNE') {
            return $this->json(['error' => 'Cet emprunt est deja retourne'], 400);
        }

        $borrow->setReturnedAt(new \DateTimeImmutable());
        $borrow->setStatus('RETOURNE');
        $borrow->getResource()->setIsAvailable(true);

        $em->flush();

        return $this->json(['message' => 'Ressource retournee avec succes']);
    }
}
