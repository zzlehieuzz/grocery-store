<?php
namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sof\ApiBundle\Entity\Liabilities
 *
 * @ORM\Table(name="liabilities", options={"comment" = "liabilities"})
* @ORM\Entity(repositoryClass="Sof\ApiBundle\Entity\LiabilitiesRepository")
 */
class Liabilities extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false, options={"comment" = "1:Id"})
     * @Assert\Type(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="customer_id", type="string", nullable=false, options={"comment" = "2:customer_id"})
     * @Assert\Type(type="integer")
     */
    private $customerId;

    /**
     * @ORM\Column(name="invoice_id", type="string", nullable=false, options={"comment" = "3:invoice_id"})
     * @Assert\Type(type="integer")
     */
    private $invoiceId;

    /**
     * @ORM\Column(name="name", type="string", nullable=false, options={"comment" = "4:name"})
     * @Assert\Type(type="string")
     */
    private $name;

    /**
     * @ORM\Column(name="amount", type="string", nullable=false, options={"comment" = "5:amount"})
     * @Assert\Type(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(name="price", type="decimal", precision=16, scale=2, nullable=false, options={"comment" = "6:price"})
     * @Assert\Type(type="decimal")
     */
    private $price;

    /**
     * @ORM\Column(name="description", type="string", nullable=true, options={"comment" = "7:description"})
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
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param mixed $customerId
     */
    public function setCustomerId($customerId)
    {
        $this->customerId = $customerId;
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
