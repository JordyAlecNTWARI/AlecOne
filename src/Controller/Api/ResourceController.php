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
