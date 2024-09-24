<?php
// src/Service/PasswordService.php

namespace App\Service;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Participant;

class PasswordService
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function encodePassword(Participant $participant, string $plainPassword): string
    {
        return $this->passwordHasher->hashPassword($participant, $plainPassword);
    }
}
