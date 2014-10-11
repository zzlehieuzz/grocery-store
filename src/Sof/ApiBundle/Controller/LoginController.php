<?php

namespace Sof\ApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Sof\ApiBundle\Lib\DateUtil;
use Sof\ApiBundle\Lib\Config;
use Sof\ApiBundle\Entity\User;


class LoginController extends AppController
{
//    public function preAction()
//    {
//        $this->getApiParams();
//    }

    /**
     * @Route("/index", name="index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {

      $user = $this->getDoctrine()
        ->getRepository('SofApiBundle:User')->find(1);

//      $user = $this->get('entity_service')->process('User:findOneById', 1);

//      $em = $this->getDoctrine()->getManager();
//      $products = $em->getRepository('AcmeStoreBundle:Product')
//        ->findAllOrderedByName();

//      $em = $this->getDoctrine()->getManager();
//      $query = $em->createQuery(
//        'SELECT p
//        FROM ApiBundle:User p
//        WHERE p.id = :id '
//      )->setParameter('id', '1');
//
//      $user = $query->getResult();

//      var_dump($user->getName()); die;

        $a = array(
            'name' => $user->getName(),
        );

        return array('name' => 10);
//        return $this->apiResponse(array());
    }

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

//    var_dump($error); die;

    $errorMessage = $error ? Config::getMessage('common.ERR.login_error') : 'Not valid!';

    return array(
      'lastUsername' => $this->get('request')->getSession()->get(SecurityContext::LAST_USERNAME),
      'error'         => $errorMessage
    );
  }
}