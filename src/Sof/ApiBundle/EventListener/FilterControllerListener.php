<?php
namespace Sof\ApiBundle\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Sof\ApiBundle\Controller\FilterControllerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class FilterControllerListener
{

    public function onKernelController(FilterControllerEvent $event)
    {
        // $request = $event->getRequest();

        $controllers = $event->getController();

        if (!is_array($controllers)) {
            return;
        }

        $controller = $controllers[0];
        // $method     = $controllers[1];

        $event->getRequest()->attributes->set('controller_name', $controller);

        if ($controller instanceof FilterControllerInterface) {
            if(!method_exists($controller, 'preAction')) return;
            $controller->preAction();
        }
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $controller = $event->getRequest()->attributes->get('controller_name');
        if ($controller instanceof FilterControllerInterface && method_exists($controller, 'catchException')) {
            $controller->catchException($event);
        } else {
            $exception = $event->getException();
            $response = new Response();
            // setup the Response object based on the caught exception
            $event->setResponse($response);
        }
    }
}
