<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UsersRepository;
use App\Tools\ServiceMailer;
use App\Tools\ServiceToken;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/reset-password")
 */
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private $serviceMailer;

    public function __construct(ServiceMailer $serviceMailer)
    {
        $this->serviceMailer = $serviceMailer;
    }

    /**
     * Display & process form to request a password reset.
     *
     * @Route("", name="app_forgot_password_request")
     */
    public function request(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData()
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/check-email", name="app_check_email")
     */
    public function checkEmail(): Response
    {
        // We prevent users from directly accessing this page
        if (!$this->canCheckEmail()) {
            return $this->redirectToRoute('app_forgot_password_request');
        }

        return $this->render('reset_password/check_email.html.twig');
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset/{idUser}/{token}", name="app_reset_password")
     */
    public function reset(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        UsersRepository $usersRepository,
        int $idUser,
        string $token = null
    ): Response {
        if ($token && $idUser) {
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password', ['idUser' => $idUser]);
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('Pas de token de réinitialisation trouvée !');
        }

        try {
            /** @var Users $user */
            $user = $usersRepository->findOneBy(['id' => $idUser]);
            ServiceToken::validateToken($token, $user);
        } catch (\Exception $e) {
            $this->addFlash('reset_password_error', sprintf(
                'Il y a un problème pour valider votre token'
            ));

            return $this->redirectToRoute('app_forgot_password_request');
        }

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $isValid = $form->get('plainPassword')->getData() === $form->get('password')->getData() ? true : false;
            if (!$isValid) {
                $this->addFlash('error', 'Les mots de passe ne sont pas identiques');

                return $this->redirectToRoute('app_reset_password');
            }
            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            $this->addFlash('success', 'Mot de passe réinitialiser, veuillez vous connecter !');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(
        string $emailFormData
    ): RedirectResponse {
        /** @var Users $user */
        $user = $this->getDoctrine()->getRepository(Users::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Marks that you are allowed to see the app_check_email page.
        $this->setCanCheckEmailInSession();

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_check_email');
        }

        try {
            $resetToken = ServiceToken::createToken();
            $user->setToken($resetToken);

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);
            $manager->flush();
        } catch (\Exception $e) {
            return $this->redirectToRoute('app_check_email');
        }

        $this->serviceMailer->sendEmailResetPasswordRequest(
            $user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@snowtrick.audrey-cesar.com', 'SnowTricks'))
                ->to($user->getEmail())
                ->subject('Demande de réinitialisation de mot de passe')
                ->htmlTemplate('reset_password/email.html.twig')
        );

        return $this->redirectToRoute('app_check_email');
    }
}
