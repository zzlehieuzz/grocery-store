<?php
namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sof\ApiBundle\Entity\Customer
 *
 * @ORM\Table(name="customer", options={"comment" = "customer"})
* @ORM\Entity(repositoryClass="Sof\ApiBundle\Entity\CustomerRepository")
 */
class Customer extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false, options={"comment" = "1:Id"})
     * @Assert\Type(type="integer")
     */
     private $id;

    /**
     * @ORM\Column(name="name", type="string", nullable=true, options={"comment" = "3:name"})
     * @Assert\Type(type="string")
     */
     private $name;

    /**
     * @ORM\Column(name="code", type="string", nullable=false, options={"comment" = "3:code"})
     * @Assert\Type(type="string")
     */
    private $code;

    /**
     * @ORM\Column(name="phone_number", type="string", nullable=true, options={"comment" = "3:phone_number"})
     * @Assert\Type(type="string")
     */
    private $phoneNumber;

    /**
     * @ORM\Column(name="address", type="string", nullable=true, options={"comment" = "3:address"})
     * @Assert\Type(type="string")
     */
    private $address;

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
     * @param mixed $code
     */
    public function setCode($code)
    {
      $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
      return $this->code;
    }

    /**
     * @param mixed $phoneNumber
     */
    public function setPhoneNumber($phoneNumber)
    {
      $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return mixed
     */
    public function getPhoneNumber()
    {
      return $this->phoneNumber;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
      $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
      return $this->address;
    }

}
