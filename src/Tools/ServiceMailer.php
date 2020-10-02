<?php

namespace App\Tools;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ServiceMailer.
 */
class ServiceMailer
{
    private $mailer;
    private $entityManager;

    public function __construct(MailerInterface $mailer, EntityManagerInterface $manager)
    {
        $this->mailer = $mailer;
        $this->entityManager = $manager;
    }

    /**
     * Register confirmation email.
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmailConfirmation(UserInterface $user, TemplatedEmail $email): void
    {
        $context = $email->getContext();
        $context['idUser'] = $user->getId();
        $context['token'] = $user->getToken();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * @param string $uriToken
     * @param UserInterface $user
     * @return bool
     */

}
