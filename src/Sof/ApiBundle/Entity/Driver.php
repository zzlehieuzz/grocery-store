<?php
namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sof\ApiBundle\Entity\Driver
 *
 * @ORM\Table(name="driver", options={"comment" = "driver"})
* @ORM\Entity(repositoryClass="Sof\ApiBundle\Entity\DriverRepository")
 */
class Driver extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false, options={"comment" = "1:Id"})
     * @Assert\Type(type="integer")
     */
     private $id;

    /**
     * @ORM\Column(name="name", type="string", nullable=false, options={"comment" = "3:name"})
     * @Assert\Type(type="string")
     */
     private $name;

    /**
     * @ORM\Column(name="number_plate", type="string", nullable=false, options={"comment" = "3:number_plate"})
     * @Assert\Type(type="string")
     */
    private $numberPlate;

    public function __construct()
    {
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
      $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
      return $this->id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
      $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
      return $this->name;
    }

    /**
     * @param mixed $numberPlate
     */
    public function setNumberPlate($numberPlate)
    {
      $this->numberPlate = $numberPlate;
    }

    /**
     * @return mixed
     */
    public function getNumberPlate()
    {
      return $this->numberPlate;
    }

}
