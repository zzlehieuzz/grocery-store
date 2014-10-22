<?php
namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Sof\ApiBundle\Entity\User
 *
 * @ORM\Table(name="user", options={"comment" = "user"})
* @ORM\Entity(repositoryClass="Sof\ApiBundle\Entity\UserRepository")
 */
class User extends BaseEntity implements UserInterface, \Serializable
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
     * @ORM\Column(name="name", type="string", nullable=true, options={"comment" = "5:name"})
     * @Assert\Type(type="string")
     */
    private $name;

    /**
     * @ORM\Column(name="email", type="string", nullable=true, options={"comment" = "6:email"})
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

  /**
   * @see \Serializable::serialize()
   */
  public function serialize()
  {
    return serialize(array(
      $this->id,
    ));
  }

  /**
   * @see \Serializable::unserialize()
   */
  public function unserialize($serialized)
  {
    list (
      $this->id,
      ) = unserialize($serialized);
  }

  /**
   * Returns the roles granted to the user.
   *
   * <code>
   * public function getRoles()
   * {
   *     return array('ROLE_USER');
   * }
   * </code>
   *
   * Alternatively, the roles might be stored on a ``roles`` property,
   * and populated in any number of different ways when the user object
   * is created.
   *
   * @return Role[] The user roles
   */
  public function getRoles()
  {
    return array();
  }

  /**
   * Returns the salt that was originally used to encode the password.
   *
   * This can return null if the password was not encoded using a salt.
   *
   * @return string|null The salt
   */
  public function getSalt()
  {
    // TODO: Implement getSalt() method.
  }

  /**
   * Removes sensitive data from the user.
   *
   * This is important if, at any given point, sensitive information like
   * the plain-text password is stored on this object.
   */
  public function eraseCredentials()
  {
    // TODO: Implement eraseCredentials() method.
  }
}
