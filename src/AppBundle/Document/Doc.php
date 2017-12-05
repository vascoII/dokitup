<?php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;


/**
 * @ODM\Document(repositoryClass="AppBundle\Repository\DocRepository")
 * @MongoDBUnique(fields="fileName")
 */
class Doc
{
    /**
     * @var MongoId $id
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;
    
    /**
     * NotBlank :: Document unique key
     * @ODM\Field(name="fileName", type="string")
     */
    private $fileName;

    /**
     * NotBlank
     * @ODM\Field(name="fileUrl", type="string")
     */
    private $fileUrl;

    /**
     * NotBlank
     * @ODM\Field(name="year", type="date")
     */
    private $year;

    /**
     * 
     * @ODM\Field(name="boolViewer", type="boolean")
     */
    private $boolViewer;
    
    /**
     * 
     * @ODM\Field(name="boolOwner", type="boolean")
     */
    private $boolOwner;
    
    
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
    protected $updatedByOwner;
    
    /** 
     * @ODM\Field(type="date", nullable=true) 
     */
    protected $updatedByOwnerAt;
    
    /**
     * @var $user User
     * @ODM\ReferenceOne(targetDocument="User")
     */
    protected $updatedByViewer;
    
    /** 
     * 
     * @ODM\Field(type="date", nullable=true) 
     */
    protected $updatedByViewerAt;

    
    public function __construct() 
    {
        $this->createdAt = new \DateTime();
        $this->updatedByOwnerAt = null;
        $this->updatedByViewerAt = null;
        $this->boolOwner = false;
        $this->boolViewer = false;
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
     * Set fileName
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Get fileName
     *
     * @return string $fileName
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set fileUrl
     *
     * @param string $fileUrl
     * @return $this
     */
    public function setFileUrl($fileUrl)
    {
        $this->fileUrl = $fileUrl;
        return $this;
    }

    /**
     * Get fileUrl
     *
     * @return string $fileUrl
     */
    public function getFileUrl()
    {
        return $this->fileUrl;
    }

    /**
     * Set boolViewer
     *
     * @param boolean $boolViewer
     * @return $this
     */
    public function setBoolViewer($boolViewer)
    {
        $this->boolViewer = $boolViewer;
        return $this;
    }

    /**
     * Get boolViewer
     *
     * @return boolean $boolViewer
     */
    public function getBoolViewer()
    {
        return $this->boolViewer;
    }

    /**
     * Set boolOwner
     *
     * @param boolean $boolOwner
     * @return $this
     */
    public function setBoolOwner($boolOwner)
    {
        $this->boolOwner = $boolOwner;
        return $this;
    }

    /**
     * Get boolOwner
     *
     * @return boolean $boolOwner
     */
    public function getBoolOwner()
    {
        return $this->boolOwner;
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
     * Set year
     *
     * @param \DateTime $year
     * @return $this
     */
    public function setYear($year)
    {
        $this->year = $year;
        return $this;
    }

    /**
     * Get $year
     *
     * @return \DateTime $year
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set updatedByOwner
     *
     * @param User $updatedByOwner
     * @return $this
     */
    public function setUpdatedByOwner(User $updatedByOwner)
    {
        $this->updatedByOwner = $updatedByOwner;
        return $this;
    }

    /**
     * Get updatedByOwner
     *
     * @return User $updatedByOwner
     */
    public function getUpdatedByOwner()
    {
        return $this->updatedByOwner;
    }

    /**
     * Set updatedByOwnerAt
     *
     * @param \DateTime $updatedByOwnerAt
     * @return $this
     */
    public function setUpdatedByOwnerAt($updatedByOwnerAt)
    {
        $this->updatedByOwnerAt = $updatedByOwnerAt;
        return $this;
    }

    /**
     * Get updatedByOwnerAt
     *
     * @return \DateTime $updatedByOwnerAt
     */
    public function getUpdatedByOwnerAt()
    {
        return $this->updatedByOwnerAt;
    }

    /**
     * Set updatedByViewer
     *
     * @param User $updatedByViewer
     * @return $this
     */
    public function setUpdatedByViewer(User $updatedByViewer)
    {
        $this->updatedByViewer = $updatedByViewer;
        return $this;
    }

    /**
     * Get updatedByViewer
     *
     * @return User $updatedByViewer
     */
    public function getUpdatedByViewer()
    {
        return $this->updatedByViewer;
    }

    /**
     * Set updatedByViewerAt
     *
     * @param \DateTime $updatedByViewerAt
     * @return $this
     */
    public function setUpdatedByViewerAt($updatedByViewerAt)
    {
        $this->updatedByViewerAt = $updatedByViewerAt;
        return $this;
    }

    /**
     * Get updatedByViewerAt
     *
     * @return \DateTime $updatedByViewerAt
     */
    public function getUpdatedByViewerAt()
    {
        return $this->updatedByViewerAt;
    }
}
