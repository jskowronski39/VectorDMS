<?php

/*
 * This file is part of the Vector DMS package.
 *
 * (c) Jakub Skowroński
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Form\Security\LoginType;
use App\Repository\User\UserRepository;
use App\Security\Authenticator\FormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("", name="app_security")
 */
class SecurityController extends AbstractController
{
    protected EntityManagerInterface $entityManager;
    protected UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/login", name="_login")
     *
     * @see FormAuthenticator
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirect User if exists (is authenticated)
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home_index');
        }

        $formType = $this->createForm(LoginType::class);
        $usernameType = $formType->get('username');

        $lastUsername = $authenticationUtils->getLastUsername();
        $usernameType->setData($lastUsername);

        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $formType->addError(new FormError($error->getMessage()));
        }

        return $this->render('security/login.html.twig', [
            'form' => $formType->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
