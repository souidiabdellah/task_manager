<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\enum\TaskStatusEnum;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Post(
            denormalizationContext: ['groups' => ['task:write']],
            validationContext: ['groups' => ['Default', 'task:creation']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['task:read']]
        ),
        new Get(
            normalizationContext: ['groups' => ['task:read', 'task:item:get']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['task:write']],
            validationContext: ['groups' => ['Default', 'task:update']]
        ),
        new Delete()
    ],
    normalizationContext: ['groups' => ['task:read']],
    denormalizationContext: ['groups' => ['task:write']]
)]
class Task
{
    public function __construct()
    {
        $this->status = TaskStatusEnum::PENDING->value;
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['task:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(groups: ['task:creation'])]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Title must be at least {{ limit }} characters long',
        maxMessage: 'Title cannot be longer than {{ limit }} characters',
        groups: ['task:creation', 'task:update']
    )]
    #[Groups(['task:read', 'task:write'])]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(groups: ['task:creation'])]
    #[Groups(['task:read', 'task:write'])]
    private string $description;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['task:read', 'task:write'])]
    private string $status;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(groups: ['task:creation'])]
    #[Assert\GreaterThanOrEqual(
        'today',
        message: 'Due date must be today or in the future',
        groups: ['task:creation', 'task:update']
    )]
    #[Groups(['task:read', 'task:write'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(nullable: false)]
    #[Groups(['task:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['task:read', 'task:write'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): TaskStatusEnum
    {
        return TaskStatusEnum::from($this->status);
    }

    public function setStatus(TaskStatusEnum|string $status): self
    {
        if (empty($status)) {
            throw new \InvalidArgumentException('Status cannot be empty');
        }
        $this->status = $status instanceof TaskStatusEnum ? $status->value : $status;
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

    public function getCreatedAt(): \DateTimeImmutable
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

    #[ORM\PrePersist]
    private function ensureInitialization(): void
    {
        if (!isset($this->status)) {
            $this->status = TaskStatusEnum::PENDING->value;
        }
        if (!isset($this->createdAt)) {
            $this->createdAt = new \DateTimeImmutable();
        }
    }
}