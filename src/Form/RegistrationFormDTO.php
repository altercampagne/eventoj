<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormDTO
{
    #[NotBlank]
    public string $email;
    #[NotBlank]
    public string $name;
    #[NotBlank]
    #[Length(min: 6, max: 4096)]
    public string $password;
}
