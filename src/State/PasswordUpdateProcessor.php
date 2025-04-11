<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\DTO\PasswordUpdateDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PasswordUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): User
    {
        /** @var PasswordUpdateDto $input */
        $input = $data;

        $userId = $uriVariables['id'] ?? null;
        if (!$userId) {
            throw new BadRequestHttpException('User ID is missing.');
        }

        $user = $this->em->getRepository(User::class)->find($userId);

        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        if (!$this->passwordHasher->isPasswordValid($user, $input->oldPassword)) {
            throw new BadRequestHttpException('Old password is incorrect.');
        }

        $hashedNewPassword = $this->passwordHasher->hashPassword($user, $input->newPassword);
        $user->setPassword($hashedNewPassword);

        dump($this->em->contains($user));
        $this->em->flush();

        return $user;
    }
}