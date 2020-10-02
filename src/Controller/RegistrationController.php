<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Tools\ServiceMailer;
use App\Tools\ServiceToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    private $serviceMailer;

    public function __construct(ServiceMailer $serviceMailer)
    {
        $this->serviceMailer = $serviceMailer;
    }

    /**
     * Registration.
     *
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setCreatedAt(new \DateTime('now'));
            $user->setToken(ServiceToken::createToken());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->serviceMailer->sendEmailConfirmation(
                $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@snowtrick.audrey-cesar.com', 'SnowTricks'))
                    ->to($user->getEmail())
                    ->subject('Confirmer votre adresse mail')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            $this->addFlash('success', 'Compte crée avec succès ! Valider votre adresse mail pour vous connecter ! ');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Verify User Email.
     *
     * @param $idUser
     * @param $token
     * @Route("/verify/email/{idUser}/{token}", name="app_verify_email")
     * @return Response
     */
    public function verifyUserEmail(
        EntityManagerInterface $manager,
        UsersRepository $usersRepository,
        $idUser,
        $token
    ): Response {
        try {
            /** @var Users $user */
            $user = $usersRepository->findOneBy(['id' => $idUser]);
            $isValid = ServiceToken::validateToken($token, $user);

            if ($isValid) {
                $user->setIsVerified(true);

                $manager->persist($user);
                $manager->flush();
            }
        } catch (\Exception $exception) {
            $this->addFlash('verify_email_error', $exception->getMessage());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre adresse email a été vérifié avec succès !');

        return $this->redirectToRoute('app_login');
    }
}
