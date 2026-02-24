<?php

declare(strict_types=1);

namespace App\Admin\Form\RegistrationCreate;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

final class ChooseUserFormDTO
{
    #[Assert\NotNull]
    public User $user;
}
