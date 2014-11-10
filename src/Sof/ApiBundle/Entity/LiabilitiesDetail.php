<?php
namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sof\ApiBundle\Entity\LiabilitiesDetail
 *
 * @ORM\Table(name="liabilities_detail", options={"comment" = "liabilities_detail"})
 * @ORM\Entity(repositoryClass="Sof\ApiBundle\Entity\LiabilitiesDetailRepository")
 */
class LiabilitiesDetail extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false, options={"comment" = "1:Id"})
     * @Assert\Type(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="liabilities_id", type="integer", nullable=false, options={"comment" = "2:liabilities_id"})
     * @Assert\Type(type="integer")
     */
    private $liabilitiesId;

    /**
     * @ORM\Column(name="name", type="string", nullable=false, options={"comment" = "3:name"})
     * @Assert\Type(type="string")
     */
    private $name;

    /**
     * @ORM\Column(name="amount", type="string", nullable=false, options={"comment" = "4:amount"})
     * @Assert\Type(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(name="price", type="decimal", precision=16, scale=2, nullable=false, options={"comment" = "5:price"})
     * @Assert\Type(type="decimal")
     */
    private $price;

    /**
     * @ORM\Column(name="description", type="string", nullable=true, options={"comment" = "6:description"})
     * @Assert\Type(type="string")
     */
    private $description;

    public function __construct()
    {
      $this->amount = 0;
      $this->price  = 0;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
    public function getLiabilitiesId()
    {
        return $this->liabilitiesId;
    }

    /**
     * @param mixed $liabilitiesId
     */
    public function setLiabilitiesId($liabilitiesId)
    {
        $this->liabilitiesId = $liabilitiesId;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
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
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }
}
