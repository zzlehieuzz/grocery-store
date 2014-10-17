<?php
namespace Sof\ApiBundle\Component\Authentication\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManager;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
  private $router;
  private static $key;

  public function __construct(RouterInterface $router, EntityManager $em, $container) {

    self::$key = '_security.secured_area.target_path';

    $this->router = $router;
    $this->em = $em;
    $this->session = $container->get('session');

  }

  public function onAuthenticationSuccess(Request $request, TokenInterface $token)
  {
    echo 'Ok'; exit;

      $url = $this->router->generate('homepage');
      $urlHost = 'http://'.$request->headers->get('host');
      $response = new RedirectResponse($urlHost.$url);

    return $response;
  }

}