<?php

namespace App\Entity;

use App\Repository\BorrowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BorrowRepository::class)]
class Borrow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $borrowedAt = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dueAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $returnedAt = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'borrows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'borrows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Resource $resource = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorrowedAt(): ?\DateTimeImmutable
    {
        return $this->borrowedAt;
    }

    public function setBorrowedAt(\DateTimeImmutable $borrowedAt): static
    {
        $this->borrowedAt = $borrowedAt;
        return $this;
    }

    public function getDueAt(): ?\DateTime
    {
        return $this->dueAt;
    }

    public function setDueAt(\DateTime $dueAt): static
    {
        $this->dueAt = $dueAt;
        return $this;
    }

    public function getReturnedAt(): ?\DateTimeImmutable
    {
        return $this->returnedAt;
    }

    public function setReturnedAt(?\DateTimeImmutable $returnedAt): static
    {
        $this->returnedAt = $returnedAt;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getResource(): ?Resource
    {
        return $this->resource;
    }

    public function setResource(?Resource $resource): static
    {
        $this->resource = $resource;
        return $this;
    }
}
