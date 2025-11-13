<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    protected const HEADER_AUTH_TOKEN = 'AUTH-TOKEN';

    public function supports(Request $request): ?bool
    {
        return $request->headers->has(self::HEADER_AUTH_TOKEN);
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get(self::HEADER_AUTH_TOKEN);

        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('Auth token not found (header: "{{ header }}")', [
                '{{ header }}' => self::HEADER_AUTH_TOKEN,
            ]);
        }

        return new SelfValidatingPassport(new UserBadge($apiToken));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        throw $exception;
    }
}
