<?php

namespace App\Tests\Integration;

use App\Entity\Resource;
use App\Repository\ResourceRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ResourceRepositoryTest extends KernelTestCase
{
    private ResourceRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(ResourceRepository::class);
    }

    public function testFindAll(): void
    {
        $resources = $this->repository->findAll();
        $this->assertIsArray($resources);
    }

    public function testFindByType(): void
    {
        $resources = $this->repository->findBy(['type' => 'Formation']);
        $this->assertIsArray($resources);
        foreach ($resources as $resource) {
            $this->assertEquals('Formation', $resource->getType());
        }
    }
}
