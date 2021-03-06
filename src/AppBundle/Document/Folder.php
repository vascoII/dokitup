<?php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;


/**
 * @ODM\Document(repositoryClass="AppBundle\Repository\FolderRepository")
 * @MongoDBUnique(fields="name")
 */
class Folder
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
     * @ODM\ReferenceMany(targetDocument="Access")
     */
    protected $accesses;
	
	/**
     * @ODM\ReferenceMany(targetDocument="Company")
     */
    protected $companies;
    
    /**
     * @ODM\ReferenceMany(targetDocument="Doc", cascade="all")
     */
    protected $docs;
    
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
        $this->docs = new ArrayCollection();
		$this->companies = new ArrayCollection();
        $this->accesses = new ArrayCollection();
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
     * Add access
     *
     * @param Access $access
     */
    public function addAccess(Access $access)
    {
        $this->accesses[] = $access;
    }

    /**
     * Remove access
     *
     * @param Access $access
     */
    public function removeAccess(Access $access)
    {
        $this->accesses->removeElement($access);
    }

    /**
     * Get accesses
     *
     * @return \Doctrine\Common\Collections\Collection $accesses
     */
    public function getAccesses()
    {
        return $this->accesses;
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
     * Add doc
     *
     * @param Doc $doc
     */
    public function addDoc(Doc $doc)
    {
        $this->docs[] = $doc;
    }

    /**
     * Remove doc
     *
     * @param Doc $doc
     */
    public function removeDoc(Doc $doc)
    {
        $this->docs->removeElement($doc);
    }

    /**
     * Get docs
     *
     * @return \Doctrine\Common\Collections\Collection $docs
     */
    public function getDocs()
    {
        return $this->docs;
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
