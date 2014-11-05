<?php
namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sof\ApiBundle\Entity\Product
 *
 * @ORM\Table(name="product", options={"comment" = "product"})
 * @ORM\Entity(repositoryClass="Sof\ApiBundle\Entity\ProductRepository")
 */
class Product extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false, options={"comment" = "1:Id"})
     * @Assert\Type(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="product_unit_id", type="integer", nullable=false, options={"comment" = "2:product_unit_id"})
     * @Assert\Type(type="integer")
     */
    private $productUnitId;

    /**
     * @ORM\Column(name="name", type="string", nullable=false, options={"comment" = "3:name"})
     * @Assert\Type(type="string")
     */
    private $name;

    /**
     * @ORM\Column(name="code", type="string", nullable=false, options={"comment" = "4:code"})
     * @Assert\Type(type="string")
     */
    private $code;

    /**
     * @ORM\Column(name="original_price", type="decimal", precision=16, scale=2, nullable=false, options={"comment" = "5:original_price"})
     * @Assert\Type(type="decimal")
     */
    private $originalPrice;

    /**
     * @ORM\Column(name="sale_price", type="decimal", precision=16, scale=2, nullable=false, options={"comment" = "6:sale_price"})
     * @Assert\Type(type="decimal")
     */
    private $salePrice;

    public function __construct()
    {
      $this->originalPrice = 0;
      $this->salePrice = 0;
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
     * @return mixed
     */
    public function getProductUnitId()
    {
        return $this->productUnitId;
    }

    /**
     * @param mixed $productUnitId
     */
    public function setProductUnitId($productUnitId)
    {
        $this->productUnitId = $productUnitId;
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
     * @return mixed
     */
    public function getOriginalPrice()
    {
      return $this->originalPrice;
    }

    /**
     * @param mixed $originalPrice
     */
    public function setOriginalPrice($originalPrice)
    {
      $this->originalPrice = $originalPrice;
    }

    /**
     * @return mixed
     */
    public function getSalePrice()
    {
      return $this->salePrice;
    }

    /**
     * @param mixed $salePrice
     */
    public function setSalePrice($salePrice)
    {
      $this->salePrice = $salePrice;
    }
}
