<?php

namespace App\Tests\Unit;

use App\Entity\Resource;
use PHPUnit\Framework\TestCase;

class ResourceTest extends TestCase
{
    public function testGetPublishedAtString(): void
    {
        $resource = new Resource();
        $date = new \DateTime('2026-01-15');
        $resource->setPublishedAt($date);

        $this->assertEquals('2026-01-15', $resource->getPublishedAt()->format('Y-m-d'));
    }

    public function testIsAvailableByDefault(): void
    {
        $resource = new Resource();
        $resource->setIsAvailable(true);

        $this->assertTrue($resource->isAvailable());
    }

    public function testSetTitle(): void
    {
        $resource = new Resource();
        $resource->setTitle('Introduction à TypeScript');

        $this->assertEquals('Introduction à TypeScript', $resource->getTitle());
    }

    public function testSetType(): void
    {
        $resource = new Resource();
        $resource->setType('Formation');

        $this->assertEquals('Formation', $resource->getType());
    }
}
