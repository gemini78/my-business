<?php

namespace App\Security;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class LoginFormAuthenticator extends AbstractAuthenticator
{

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'security_login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $request->request->all()['login'];
        return new Passport( 
            new UserBadge($credentials['email']),           
           new PasswordCredentials($credentials['password'])    
        ); 
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse('/');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $errorMsg = "Erreur d'authentification"; 

        if ($exception->getMessage() === "Bad credentials.") 
        {
            $errorMsg = "Cette adresse email n'est pas connue.";
        } elseif ($exception->getMessage() === "The presented password is invalid.") 
        {
            $errorMsg = "Le mot de passe et l'adresse email ne correspondent pas";
        }

        $exception = new AuthenticationException($errorMsg);
        $request->attributes->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $exception);
        $request->attributes->set(SecurityRequestAttributes::LAST_USERNAME, $request->request->all()['login']['email']);

        return null;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
       return new RedirectResponse('/login');
    }
}
