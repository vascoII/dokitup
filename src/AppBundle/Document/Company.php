<?php
namespace AppBundle\Document;

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
     * @ODM\ReferenceMany(targetDocument="User", cascade="all")
     */
    protected $users;

    /**
     * @ODM\ReferenceMany(targetDocument="Folder", mappedBy="companies")
     */
    protected $folders;
    
    /**
     * @var User $user 
     * @ODM\ReferenceOne(targetDocument="User")
     */
    protected $createdBy;
    
    /** 
     * @var \DateTime createdAt
     * @ODM\Field(type="date") 
     */
    protected $createdAt;
    
    /**
     * @var User $user
     * @ODM\ReferenceOne(targetDocument="User")
     */
    protected $updatedBy;
    
    /** 
     * @var \DateTime updateAt
     * @ODM\Field(type="date", nullable=true) 
     */
    protected $updatedAt;

    
    public function __construct() 
    {
        $this->users = new ArrayCollection();
        $this->folders = new ArrayCollection();
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
     * Add user
     *
     * @param User $user
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;
    }

    /**
     * Remove user
     *
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
        $user->removeCompany($this);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection $users
     */
    public function getUsers()
    {
        return $this->users;
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
