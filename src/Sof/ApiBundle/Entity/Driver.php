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
     * @ORM\Column(name="role_id", type="smallint", nullable=false, options={"comment" = "2:role_id"})
     * @Assert\Type(type="smallint")
     */
     private $role_id;

    /**
     * @ORM\Column(name="username", type="string", nullable=false, options={"comment" = "3:username"})
     * @Assert\Type(type="string")
     */
     private $userName;

    /**
     * @ORM\Column(name="password", type="string", nullable=false, options={"comment" = "4:password"})
     * @Assert\Type(type="string")
     */
     private $password;

    /**
     * @ORM\Column(name="name", type="string", nullable=false, options={"comment" = "5:name"})
     * @Assert\Type(type="string")
     */
    private $name;

    /**
     * @ORM\Column(name="email", type="string", nullable=false, options={"comment" = "6:email"})
     * @Assert\Type(type="string")
     */
    private $email;

    public function __construct()
    {
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
      $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
      return $this->email;
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
     * @param mixed $password
     */
    public function setPassword($password)
    {
      if ($password !== NULL) {
        $this->password = md5($password);
      }
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
      return $this->password;
    }

    /**
     * @param mixed $role_id
     */
    public function setRoleId($role_id)
    {
      $this->role_id = $role_id;
    }

    /**
     * @return mixed
     */
    public function getRoleId()
    {
      return $this->role_id;
    }

    /**
     * @param mixed $userName
     */
    public function setUserName($userName)
    {
      $this->userName = $userName;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
      return $this->userName;
    }
}
