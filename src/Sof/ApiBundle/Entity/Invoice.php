<?php
namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sof\ApiBundle\Entity\Invoice
 *
 * @ORM\Table(name="invoice", options={"comment" = "invoice"})
* @ORM\Entity(repositoryClass="Sof\ApiBundle\Entity\InvoiceRepository")
 */
class Invoice extends BaseEntity
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @ORM\Column(name="id", type="integer", nullable=false, options={"comment" = "1:Id"})
   * @Assert\Type(type="integer")
   */
  private $id;

  /**
   * @ORM\Column(name="invoice_number", type="string", nullable=true, options={"comment" = "2:invoice_number"})
   * @Assert\Type(type="string")
   */
  private $invoiceNumber;

    /**
     * @ORM\Column(name="create_invoice_date", type="datetime", nullable=true)
     * @Assert\Type(type="datetime")
     */
    private $createInvoiceDate;

    /**
     * @ORM\Column(name="subject", type="integer", nullable=false, options={"comment" = "4:subject"})
     * @Assert\Type(type="integer")
     */
    private $subject;

    /**
     * @ORM\Column(name="delivery_receiver_man", type="string", nullable=true, options={"comment" = "5:delivery_receiver_man"})
     * @Assert\Type(type="string")
     */
    private $deliveryReceiverMan;

    /**
     * @ORM\Column(name="create_invoice_man", type="string", nullable=true, options={"comment" = "6:create_invoice_man"})
     * @Assert\Type(type="string")
     */
    private $createInvoiceMan;

    /**
     * @ORM\Column(name="address", type="string", nullable=true, options={"comment" = "7:address"})
     * @Assert\Type(type="string")
     */
    private $address;

    /**
     * @ORM\Column(name="phone_number", type="string", nullable=true, options={"comment" = "8:phone_number"})
     * @Assert\Type(type="string")
     */
    private $phoneNumber;

    /**
     * @ORM\Column(name="invoice_type", type="integer", nullable=false, options={"comment" = "9:invoice_type"})
     * @Assert\Type(type="integer")
     */
    private $invoiceType;

    /**
     * @ORM\Column(name="payment_status", type="integer", nullable=false, options={"comment" = "10:payment_status"})
     * @Assert\Type(type="integer")
     */
    private $paymentStatus;

    /**
     * @ORM\Column(name="amount", type="integer", nullable=true, options={"comment" = "11:amount"})
     * @Assert\Type(type="integer")
     */
    private $amount;

    /**
     * @ORM\Column(name="total_amount", type="integer", nullable=true, options={"comment" = "12:total_amount"})
     * @Assert\Type(type="integer")
     */
    private $totalAmount;

    /**
     * @ORM\Column(name="description", type="string", nullable=true, options={"comment" = "13:description"})
     * @Assert\Type(type="string")
     */
    private $description;

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
   * @param mixed $invoiceNumber
   */
  public function setInvoiceNumber($invoiceNumber)
  {
    $this->invoiceNumber = $invoiceNumber;
  }

  /**
   * @return mixed
   */
  public function getInvoiceNumber()
  {
    return $this->invoiceNumber;
  }

  /**
   * @param mixed $createInvoiceDate
   */
  public function setCreateInvoiceDate($createInvoiceDate)
  {
    $this->createInvoiceDate = $createInvoiceDate;
  }

  /**
   * @return mixed
   */
  public function getCreateInvoiceDate()
  {
    return $this->createInvoiceDate;
  }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $deliveryReceiverMan
     */
    public function setDeliveryReceiverMan($deliveryReceiverMan)
    {
        $this->deliveryReceiverMan = $deliveryReceiverMan;
    }

    /**
     * @return mixed
     */
    public function getDeliveryReceiverMan()
    {
        return $this->deliveryReceiverMan;
    }

    /**
     * @param mixed $createInvoiceMan
     */
    public function setCreateInvoiceMan($createInvoiceMan)
    {
        $this->createInvoiceMan = $createInvoiceMan;
    }

    /**
     * @return mixed
     */
    public function getCreateInvoiceMan()
    {
        return $this->createInvoiceMan;
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
     * @param mixed $invoiceType
     */
    public function setInvoiceType($invoiceType)
    {
        $this->invoiceType = $invoiceType;
    }

    /**
     * @return mixed
     */
    public function getInvoiceType()
    {
        return $this->invoiceType;
    }

    /**
     * @param mixed $paymentStatus
     */
    public function setPaymentStatus($paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * @return mixed
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
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
     * @param mixed $totalAmount
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @return mixed
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
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
    public function getDescription()
    {
        return $this->description;
    }


}
