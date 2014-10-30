<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;


class LoginController extends BaseController
{
    /**
    * @Route("/login", name="user_login")
    * @Template()
    */
    public function loginAction()
    {
        if ($this->get('request')->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
          $error = $this->get('request')->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
          $error = $this->get('request')->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

//        $errorMessage = $error ? Config::getMessage('common.ERR.login_error') : '';
        $errorMessage = $error ? 'User name or password is not valid!' : null;

        return array(
          'lastUsername' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
          'error'         => $errorMessage
        );
    }
}