<?php
namespace AppBundle\Document;

use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ODM\Document(repositoryClass="AppBundle\Repository\CompanyRepository")
 * @MongoDBUnique(fields="name")
 */
class Company
{
    /**
     * @var MongoId $id
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;
    
    /**
     * Company unique key
     * @ODM\Field(name="name", type="string")
     */
    protected $name;

    /**
     * NotBlank
     * @ODM\Field(name="lastName", type="string", nullable=true)
     */
    protected $address;
    
    /**
     * @var CompanyType companyType 
     * @ODM\ReferenceOne(targetDocument="CompanyType")
     */
    protected $companyType;
    
    /**
     * 
     * @ODM\Field(name="attributes", type="collection")
     */
    protected $attributes;
    
    /**
     * @ODM\ReferenceMany(targetDocument="Folder", mappedBy="companies")
     */
    protected $folders;
    
    /**
     * @var User $user 
     * @ODM\ReferenceOne(targetDocument="User")
     */
    protected $createBy;
    
    /** 
     * @var \DateTime createdAt
     * @ODM\Field(type="date") 
     */
    protected $createdAt;
    
    /**
     * @var User $user
     * @ODM\ReferenceOne(targetDocument="User")
     */
    protected $updateBy;
    
    /** 
     * @var \DateTime updateAt
     * @ODM\Field(type="date", nullable=true) 
     */
    protected $updateAt;

    
    public function __construct() 
    {
        $this->folders = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updateAt = null;
    }

    /**
     * Get id
     *
     * @return MongoId $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get address
     *
     * @return string $address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set companyType
     *
     * @param CompanyType $companyType
     * @return $this
     */
    public function setCompanyType(CompanyType $companyType)
    {
        $this->companyType = $companyType;
        return $this;
    }

    /**
     * Get companyType
     *
     * @return CompanyType $companyType
     */
    public function getCompanyType()
    {
        return $this->companyType;
    }

    /**
     * Set attributes
     *
     * @param Collection $attributes
     * @return $this
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Get attributes
     *
     * @return Collection $attributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Add folder
     *
     * @param Folder $folder
     */
    public function addFolder(Folder $folder)
    {
        $this->folders[] = $folder;
    }

    /**
     * Remove folder
     *
     * @param Folder $folder
     */
    public function removeFolder(Folder $folder)
    {
        $this->folders->removeElement($folder);
    }

    /**
     * Get folders
     *
     * @return \Doctrine\Common\Collections\Collection $folders
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * Set createBy
     *
     * @param User $createBy
     * @return $this
     */
    public function setCreateBy(User $createBy)
    {
        $this->createBy = $createBy;
        return $this;
    }

    /**
     * Get createBy
     *
     * @return User $createBy
     */
    public function getCreateBy()
    {
        return $this->createBy;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updateBy
     *
     * @param User $updateBy
     * @return $this
     */
    public function setUpdateBy(User $updateBy)
    {
        $this->updateBy = $updateBy;
        return $this;
    }

    /**
     * Get updateBy
     *
     * @return User $updateBy
     */
    public function getUpdateBy()
    {
        return $this->updateBy;
    }

    /**
     * Set updateAt
     *
     * @param \DateTime $updateAt
     * @return $this
     */
    public function setUpdateAt($updateAt)
    {
        $this->updateAt = $updateAt;
        return $this;
    }

    /**
     * Get updateAt
     *
     * @return \DateTime $updateAt
     */
    public function getUpdateAt()
    {
        return $this->updateAt;
    }
}
