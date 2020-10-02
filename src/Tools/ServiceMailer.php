<?php

namespace App\Tools;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class ServiceMailer.
 */
class ServiceMailer
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
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

    public function sendEmailResetPasswordRequest(UserInterface $user, TemplatedEmail $email): void
    {
        $context = $email->getContext();
        $context['idUser'] = $user->getId();
        $context['token'] = $user->getToken();

        $email->context($context);

        $this->mailer->send($email);
    }
}
