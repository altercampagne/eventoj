<?php

declare(strict_types=1);

namespace App\Bridge\Sentry\EventListener;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[When(env: 'prod')]
final readonly class SentryContextListener
{
    public function __construct(
        private Security $security,
    ) {
    }

    #[AsEventListener]
    public function onRequestEvent(): void
    {
        if (null === $user = $this->security->getUser()) {
            return;
        }
        if (!$user instanceof User) {
            return;
        }
        \Sentry\configureScope(static function (\Sentry\State\Scope $scope) use ($user): void {
            $scope->setUser([
                'id' => (string) $user->getId(),
                'email' => $user->getEmail(),
            ]);
        });
    }
}
