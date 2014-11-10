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

    public function __construct()
    {
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
}
