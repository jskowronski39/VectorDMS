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

namespace App\Security\Authenticator\Dto;

use Symfony\Component\HttpFoundation\Request;

class CredentialsDto
{
    protected string $username;
    protected string $password;
    protected string $csrfToken;

    public function __construct(string $username, string $password, string $csrfToken)
    {
        $this->username = $username;
        $this->password = $password;
        $this->csrfToken = $csrfToken;
    }

    public static function createFromRequest(Request $request): self
    {
        return new self(
            $request->request->get('username'),
            $request->request->get('password'),
            $request->request->get('_csrf_token')
        );
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCsrfToken(): string
    {
        return $this->csrfToken;
    }
}
