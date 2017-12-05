<?php
namespace AppBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;


/**
 * @ODM\Document(repositoryClass="AppBundle\Repository\DocRepository")
 * @MongoDBUnique(fields="accessType", fields="company", fields="folder")
 */
class Access
{
    /**
     * @var MongoId $id
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ODM\ReferenceOne(targetDocument="AccessType")
     */
    protected $accessType;

    /**
     * @ODM\ReferenceMany(targetDocument="Company")
     */
    protected $companies;
    
    /**
     * @ODM\ReferenceOne(targetDocument="Folder")
     */
    protected $folder;
    
    /**
     * @var $user User
     * @ODM\ReferenceOne(targetDocument="User")
     */
    protected $createdBy;
    
    /** 
     * 
     * @ODM\Field(type="date") 
     */
    protected $createdAt;
    
    /**
     * @var $user User
     * @ODM\ReferenceOne(targetDocument="User")
     */
    protected $updatedBy;
    
    /** 
     * @ODM\Field(type="date", nullable=true) 
     */
    protected $updatedAt;

    
    public function __construct() 
    {
        $this->companies = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = null;
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
     * Set accessType
     *
     * @param AccessType $accessType
     * @return $this
     */
    public function setAccessType(AccessType $accessType)
    {
        $this->accessType = $accessType;
        return $this;
    }

    /**
     * Get accessType
     *
     * @return AccessType $accessType
     */
    public function getAccessType()
    {
        return $this->accessType;
    }

    /**
     * Add company
     *
     * @param Company $company
     */
    public function addCompany(Company $company)
    {
        $this->companies[] = $company;
    }

    /**
     * Remove company
     *
     * @param Company $company
     */
    public function removeCompany(Company $company)
    {
        $this->companies->removeElement($company);
    }

    /**
     * Get companies
     *
     * @return \Doctrine\Common\Collections\Collection $companies
     */
    public function getCompanies()
    {
        return $this->companies;
    }

    /**
     * Set folder
     *
     * @param Folder $folder
     * @return $this
     */
    public function setFolder(Folder $folder)
    {
        $this->folder = $folder;
        return $this;
    }

    /**
     * Get folder
     *
     * @return Folder $folder
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set createdBy
     *
     * @param User $createdBy
     * @return $this
     */
    public function setCreatedBy(User $createdBy)
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * Get createdBy
     *
     * @return User $createdBy
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
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
     * Set updatedBy
     *
     * @param User $updatedBy
     * @return $this
     */
    public function setUpdatedBy(User $updatedBy)
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return User $updatedBy
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
