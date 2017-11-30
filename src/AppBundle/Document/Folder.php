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
     * @ODM\ReferenceMany(targetDocument="Access", mappedBy="folder")
     */
    protected $accesses;
	
	/**
     * @ODM\ReferenceMany(targetDocument="Company")
     */
    protected $companies;
    
    /**
     * @ODM\ReferenceMany(targetDocument="Document", cascade="all")
     */
    protected $documents;
    
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
    protected $updatedBy;
    
    /** 
     * @var \DateTime updateAt
     * @ODM\Field(type="date", nullable=true) 
     */
    protected $updatedAt;

    
    public function __construct() 
    {
        $this->documents = new ArrayCollection();
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
     * Add document
     *
     * @param Document $document
     */
    public function addDocument(Document $document)
    {
        $this->documents[] = $document;
    }

    /**
     * Remove document
     *
     * @param Document $document
     */
    public function removeDocument(Document $document)
    {
        $this->documents->removeElement($document);
    }

    /**
     * Get documents
     *
     * @return \Doctrine\Common\Collections\Collection $documents
     */
    public function getDocuments()
    {
        return $this->documents;
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
