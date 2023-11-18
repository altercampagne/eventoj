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
    #[Length(min: 7, max: 4096, minMessage: 'Ce mot de passe est trop court ! Il doit faire au moins {{ limit }} caractères.')]
    public string $password;
}
