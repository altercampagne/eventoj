<?php

declare(strict_types=1);

namespace App\Tests\FunctionalTests\Website;

use App\Entity\ResetPasswordRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class PasswordResetTest extends WebTestCase
{
    public function testWithExistingMail(): void
    {
        $client = static::createClient();

        $email = 'change-my-password@test-only.user';

        $this->removeExistingResetPasswordRequests($email);

        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('h1', 'Se connecter');

        $client->clickLink('Mot de passe oublié ?');

        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('forgot_password_request');

        $client->submitForm('Envoyer le mail', [
            'reset_password_request_form[email]' => $email,
        ]);

        $this->assertQueuedEmailCount(1);

        /** @var TemplatedEmail $sentEmail */
        /* @phpstan-ignore-next-line */
        $sentEmail = $this->getMailerEvent()->getMessage();
        $this->assertEmailHasHeader($sentEmail, 'subject', 'Demande de réinitialisation de mot de passe');
        $this->assertEmailAddressContains($sentEmail, 'to', $email);

        $crawler = new Crawler((string) $sentEmail->getHtmlBody());
        $linkInEmail = $crawler->selectLink('Réinitialiser mon mot de passe')->link()->getUri();

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('check_email');
        $this->assertSelectorTextSame('h1', 'Demande bien reçue !');

        $client->request('GET', $linkInEmail);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('reset_password');
        $this->assertSelectorTextSame('h1', 'Mise à jour de ton mot de passe');

        $password = md5((string) time());

        $client->submitForm('Mettre à jour mon mot de passe', [
            'change_password_form[plainPassword][first]' => $password,
            'change_password_form[plainPassword][second]' => $password,
        ]);

        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('login');
        $this->assertSelectorTextSame('h1', 'Se connecter');
        $this->assertSelectorTextSame('.alert-success', 'Mot de passe mis à jour, tu peux maintenant te connecter.');

        $client->submitForm('Se connecter', [
            '_username' => $email,
            '_password' => $password,
        ]);

        $this->assertResponseRedirects();
        $response = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('homepage');
        $this->assertSelectorExists('#connected-as');
    }

    private function removeExistingResetPasswordRequests(string $email): void
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get(EntityManagerInterface::class);
        $queryBuilder = $em->getRepository(ResetPasswordRequest::class)->createQueryBuilder('r');
        $queryBuilder
            ->innerJoin('r.user', 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
        ;
        /** @var ResetPasswordRequest[] $results */
        $results = $queryBuilder->getQuery()->getResult();
        foreach ($results as $request) {
            $em->remove($request);
        }
        $em->flush();
    }
}
