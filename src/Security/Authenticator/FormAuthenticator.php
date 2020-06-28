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

namespace App\Security\Authenticator;

use App\Entity\User\UserInterface;
use App\Repository\User\UserRepository;
use App\Security\Authenticator\Dto\CredentialsDto;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    public const SUPPORTED_ATTRIBUTE = '_route';
    public const LOGIN_ROUTE_NAME = 'app_security_login';
    public const HOME_ROUTE_NAME = 'app_home_index';

    protected UserRepository $userRepository;
    protected UrlGeneratorInterface $urlGenerator;
    protected CsrfTokenManagerInterface $csrfTokenManager;
    protected UserPasswordEncoderInterface $userPasswordEncoder;
    protected TranslatorInterface $translator;

    public function __construct(
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $userPasswordEncoder,
        TranslatorInterface $translator
    ) {
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE_NAME === $request->attributes->get(self::SUPPORTED_ATTRIBUTE) && $request->isMethod(Request::METHOD_POST);
    }

    public function getCredentials(Request $request): CredentialsDto
    {
        $credentials = CredentialsDto::createFromRequest($request);

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials->getUsername()
        );

        return $credentials;
    }

    /**
     * @param CredentialsDto $credentials
     */
    public function getUser($credentials, UserProviderInterface $userProvider): UserInterface
    {
        $token = new CsrfToken('authenticate', $credentials->getCsrfToken());

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        try {
            $user = $this->userRepository->findOneByUsername($credentials->getUsername());
        } catch (\Exception $ex) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('form_error.database_connection_error_occurred', [], 'security'),
                [],
                0,
                $ex
            );
        }

        if (!$user) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('form_error.the_username_or_password_is_incorrect', [], 'security')
            );
        }

        return $user;
    }

    /**
     * @param CredentialsDto $credentials
     */
    public function checkCredentials($credentials, SymfonyUserInterface $user): bool
    {
        if (!$this->userPasswordEncoder->isPasswordValid($user, $credentials->getPassword())) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('form_error.the_username_or_password_is_incorrect', [], 'security')
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): RedirectResponse
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate(self::HOME_ROUTE_NAME));
    }

    /**
     * {@inheritdoc}
     */
    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE_NAME);
    }
}
