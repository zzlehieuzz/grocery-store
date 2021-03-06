<?php
namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sof\ApiBundle\Entity\Product
 *
 * @ORM\Table(name="unit", options={"comment" = "unit"})
* @ORM\Entity(repositoryClass="Sof\ApiBundle\Entity\ProductRepository")
 */
class Unit extends BaseEntity
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(name="id", type="integer", nullable=false, options={"comment" = "1:Id"})
   * @Assert\Type(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(name="name", type="string", nullable=false, options={"comment" = "2:name"})
   * @Assert\Type(type="string")
   */
  private $name;

  /**
   * @ORM\Column(name="code", type="string", nullable=true, options={"comment" = "3:code"})
   * @Assert\Type(type="string")
   */
  private $code;


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

}
