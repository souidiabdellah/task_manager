<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\enum\TaskStatusEnum;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]

#[ApiResource]
class Task
{
    public function __construct()
    {
        $this->status = TaskStatusEnum::PENDING->value;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $title = null; // Allow null values

    #[ORM\Column(type: Types::TEXT,nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE,nullable: true)]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    // Getter to return the status as a TaskStatusEnum
    public function getStatus(): ?TaskStatusEnum
    {
        // Return the status as an Enum object
        return $this->status ? TaskStatusEnum::from($this->status) : null;
    }

    // Setter to set the status as a string or Enum
    public function setStatus(TaskStatusEnum|string|null $status): self
    {
        if ($status instanceof TaskStatusEnum) {
            $this->status = $status->value;  // Store the string value of the Enum
        } elseif (is_string($status)) {
            $this->status = $status;  // If it's a string, store it directly
        } else {
            $this->status = null;
        }

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(\DateTimeInterface $dueDate): static
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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
    public function setCreatedAtValue(): void
    {
        // If createdAt is not set, set it to current datetime
        if ($this->createdAt === null) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }
}
