<?php

namespace App\Security;

use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;


class AuthHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface
{

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        if ($request->isXmlHttpRequest()) {
            return $response = new Response("", 200);
        } else {
            return new RedirectResponse('/');
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
        if ($request->isXmlHttpRequest()) {
            return $response = new Response("", 403);
        } else {
            return new RedirectResponse('/authorize');
        }
    }
}