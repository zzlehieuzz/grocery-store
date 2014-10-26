<?php
namespace Sof\ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Validator\Constraints as Assert;
use Sof\ApiBundle\Entity\ValueConst\BaseConst;

/**
 * Sof\ApiBundle\Entity\Module
 *
 * @ORM\Table(name="module", options={"comment" = "module"})
* @ORM\Entity(repositoryClass="Sof\ApiBundle\Entity\ModuleRepository")
 */
class Module extends BaseEntity
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
     * @ORM\Column(name="icon_cls", type="string", nullable=true, options={"comment" = "3:icon_cls"})
     * @Assert\Type(type="string")
     */
    private $iconCls;

    /**
     * @ORM\Column(name="module", type="string", nullable=true, options={"comment" = "4:module"})
     * @Assert\Type(type="string")
     */
    private $module;

    /**
     * @ORM\Column(name="sort", type="integer", nullable=false, options={"comment" = "5:sort"})
     * @Assert\Type(type="integer")
     */
    private $sort;

    /**
     * @ORM\Column(name="is_active", type="smallint", nullable=false, options={"comment" = "6:is_active"})
     * @Assert\Type(type="smallint")
     */
    private $isActive;

    public function __construct()
    {
        $this->isActive = BaseConst::FLAG_ON;
    }

    /**
     * @return mixed
     */
    public function getIconCls()
    {
        return $this->iconCls;
    }

    /**
     * @param mixed $iconCls
     */
    public function setIconCls($iconCls)
    {
        $this->iconCls = $iconCls;
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
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param mixed $module
     */
    public function setModule($module)
    {
        $this->module = $module;
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
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }
}
