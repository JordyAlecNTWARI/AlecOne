<?php

namespace App\Controller\Api;

use App\Entity\Review;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/reviews', name: 'api_reviews_')]
class ReviewController extends AbstractController
{
    #[Route('/resource/{id}', name: 'by_resource', methods: ['GET'])]
    public function byResource(int $id, ReviewRepository $repo): JsonResponse
    {
        $reviews = $repo->findBy([
            'resource' => $id,
            'isApproved' => true,
        ]);

        $data = array_map(fn(Review $r) => [
            'id' => $r->getId(),
            'rating' => $r->getRating(),
            'comment' => $r->getComment(),
            'createdAt' => $r->getCreatedAt()?->format('Y-m-d'),
            'user' => [
                'firstName' => $r->getUser()->getFirstName(),
                'lastName' => $r->getUser()->getLastName(),
            ],
        ], $reviews);

        $avg = count($reviews) > 0
            ? round(array_sum(array_column($data, 'rating')) / count($reviews), 1)
            : null;

        return $this->json([
            'reviews' => $data,
            'average' => $avg,
            'count' => count($reviews),
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);

        if (!isset($data['resourceId'], $data['rating'])) {
            return $this->json(['error' => 'Champs manquants'], 400);
        }

        if ($data['rating'] < 1 || $data['rating'] > 5) {
            return $this->json(['error' => 'La note doit etre entre 1 et 5'], 400);
        }

        $resource = $em->getRepository(\App\Entity\Resource::class)->find($data['resourceId']);
        if (!$resource) {
            return $this->json(['error' => 'Ressource introuvable'], 404);
        }

        $review = new Review();
        $review->setUser($user);
        $review->setResource($resource);
        $review->setRating($data['rating']);
        $review->setComment($data['comment'] ?? null);
        $review->setCreatedAt(new \DateTimeImmutable());
        $review->setIsApproved(false);

        $em->persist($review);
        $em->flush();

        return $this->json(['message' => 'Avis soumis, en attente de moderation'], 201);
    }
}
