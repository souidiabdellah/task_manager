<?php
namespace App\DTO;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class PasswordUpdateDto
{
    #[Groups(['password:update'])]
    #[Assert\NotBlank(message: 'Current password is required.')]
    public ?string $oldPassword = null;

    #[Groups(['password:update'])]
    #[Assert\NotBlank(message: 'New password is required.')]
    #[Assert\Length(min: 6, minMessage: 'Your new password must be at least {{ limit }} characters long.')]
    public ?string $newPassword = null;
}