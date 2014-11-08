<?php
namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sof\ApiBundle\Entity\InvoiceDetail
 *
 * @ORM\Table(name="invoice_detail", options={"comment" = "invoice_detail"})
* @ORM\Entity(repositoryClass="Sof\ApiBundle\Entity\InvoiceDetailRepository")
 */
class InvoiceDetail extends BaseEntity
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(name="id", type="integer", nullable=false, options={"comment" = "1:Id"})
   * @Assert\Type(type="integer")
   */
  private $id;

    /**
     * @ORM\Column(name="invoice_id", type="integer", nullable=false, options={"comment" = "2:invoice_id"})
     * @Assert\Type(type="integer")
     */
    private $invoiceId;

    /**
     * @ORM\Column(name="product_id", type="integer", nullable=false, options={"comment" = "3:product_id"})
     * @Assert\Type(type="integer")
     */
    private $productId;

    /**
     * @ORM\Column(name="unit", type="integer", nullable=true, options={"comment" = "4:unit"})
     * @Assert\Type(type="integer")
     */
    private $unit;

    /**
     * @ORM\Column(name="amount", type="integer", nullable=true, options={"comment" = "5:amount"})
     * @Assert\Type(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(name="price", type="decimal", precision=16, scale=2, nullable=true, options={"comment" = "6:price"})
     * @Assert\Type(type="decimal")
     */
    private $price;


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
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * @param mixed $invoiceId
     */
    public function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

  /**
   * @param mixed $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }

  /**
   * @return mixed
   */
  public function getUnit()
  {
    return $this->unit;
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
  public function getAmount()
  {
    return $this->amount;
  }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

}
