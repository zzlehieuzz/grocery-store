<?php
namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Sof\ApiBundle\Entity\ProductUnit
 *
 * @ORM\Table(name="product_unit", options={"comment" = "product_unit"})
* @ORM\Entity(repositoryClass="Sof\ApiBundle\Entity\ProductUnitRepository")
 */
class ProductUnit extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false, options={"comment" = "1:Id"})
     * @Assert\Type(type="integer")
     */
     private $id;

    /**
     * @ORM\Column(name="product_id", type="integer", nullable=false, options={"comment" = "2:product_id"})
     * @Assert\Type(type="integer")
     */
     private $productId;

    /**
     * @ORM\Column(name="unit_id1", type="integer", nullable=false, options={"comment" = "3:unit_id1"})
     * @Assert\Type(type="integer")
     */
    private $unitId1;

    /**
     * @ORM\Column(name="unit_id2", type="integer", nullable=false, options={"comment" = "4:unit_id2"})
     * @Assert\Type(type="integer")
     */
    private $unitId2;

    /**
     * @ORM\Column(name="convert_amount", type="bigint", nullable=false, options={"comment" = "5:convert_amount"})
     * @Assert\Type(type="bigint")
     */
     private $convertAmount;

    /**
     * @ORM\Column(name="description", type="string", nullable=true, options={"comment" = "5:description"})
     * @Assert\Type(type="string")
     */
    private $description;

    public function __construct()
    {
    }
    /**
     * @return mixed
     */
    public function getConvertAmount()
    {
        return $this->convertAmount;
    }

    /**
     * @param mixed $convertAmount
     */
    public function setConvertAmount($convertAmount)
    {
        $this->convertAmount = $convertAmount;
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
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @param mixed $productId
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * @return mixed
     */
    public function getUnitId1()
    {
        return $this->unitId1;
    }

    /**
     * @param mixed $unitId1
     */
    public function setUnitId1($unitId1)
    {
        $this->unitId1 = $unitId1;
    }

    /**
     * @return mixed
     */
    public function getUnitId2()
    {
        return $this->unitId2;
    }

    /**
     * @param mixed $unitId2
     */
    public function setUnitId2($unitId2)
    {
        $this->unitId2 = $unitId2;
    }
}
