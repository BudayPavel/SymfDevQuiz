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
            $result = array('success' => true);
            $response = new Response(json_encode($result), 200);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            return new RedirectResponse('/');
        }
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception) {
            $result = array('success' => false, 'message' => $exception->getMessage());
            $response = new Response(json_encode($result), 403);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
    }
}