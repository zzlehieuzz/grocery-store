<?php
namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Sof\ApiBundle\Entity\DriverInvoice
 *
 * @ORM\Table(name="driver_invoice", options={"comment" = "driver_invoice"})
 * @ORM\Entity(repositoryClass="Sof\ApiBundle\Entity\DriverInvoiceRepository")
 */
class DriverInvoice extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer", nullable=false, options={"comment" = "1:Id"})
     * @Assert\Type(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="driver_id", type="integer", nullable=true, options={"comment" = "2:driver_id"})
     * @Assert\Type(type="integer")
     */
    private $driverId;

    /**
     * @ORM\Column(name="invoice_id", type="integer", nullable=true, options={"comment" = "3:invoice_id"})
     * @Assert\Type(type="datetime")
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
    public function getDriverId()
    {
        return $this->driverId;
    }

    /**
     * @param mixed $driverId
     */
    public function setDriverId($driverId)
    {
        $this->driverId = $driverId;
    }
}
