<?php
namespace Sof\ApiBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

class DoctrineEventListener
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        /*    $token = $this->container->get('security.context')->getToken();

            if ($token) {
              $user     = $token->getUser();
              $entity   = $args->getEntity();
            }*/
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        /*    $token = $this->container->get('security.context')->getToken();

            if ($token) {
              $entityManager = $args->getEntityManager();
              $unitOfWork = $entityManager->getUnitOfWork();

              $user = $token->getUser();
              $entity = $args->getEntity();

              // push change to doctrine
              $meta = $entityManager->getClassMetadata(get_class($entity));
              $unitOfWork->recomputeSingleEntityChangeSet($meta, $entity);
            }*/
    }
}