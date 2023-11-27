<?php

declare(strict_types=1);

namespace App\Form;

use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormDTO
{
    #[NotBlank]
    public string $email;
    #[NotBlank]
    public string $firstName;
    #[NotBlank]
    public string $lastName;
    #[NotBlank]
    public \DateTimeImmutable $birthDate;
    #[NotBlank]
    #[Length(min: 7, max: 4096, minMessage: 'Ce mot de passe est trop court ! Il doit faire au moins {{ limit }} caractères.')]
    public string $password;
    #[NotBlank]
    public string $addressLine1;
    public ?string $addressLine2 = null;
    #[NotBlank]
    public string $city;
    #[NotBlank]
    public string $zipCode;
    #[NotBlank]
    public string $countryCode;
    #[AssertPhoneNumber(regionPath: 'countryCode')]
    public PhoneNumber $phoneNumber;
}
