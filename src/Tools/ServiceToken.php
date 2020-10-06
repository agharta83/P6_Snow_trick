<?php

namespace App\Tools;

use Symfony\Component\Security\Core\User\UserInterface;

class ServiceToken
{
    /**
     * Generate unique token
     * @return string
     */
    public static function createToken(): string
    {
        return md5(uniqid());
    }

    public static function validateToken(string $uriToken, UserInterface $user): bool
    {
        $knowToken = $user->getToken();

        return null !== $uriToken && $uriToken === $knowToken;
    }
}
