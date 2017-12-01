<?php
namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;

/**
 * @ODM\Document(repositoryClass="AppBundle\Repository\AuthTokenRepository")
 * @MongoDBUnique(fields="value")
 */
class AuthToken
{
    /**
     * @var MongoId $id
     * @ODM\Id(strategy="AUTO")
     *
     */
    protected $id;

    /**
     * @var string $value
     * @ODM\Field(name="value", type="string")
     */
    protected $value;

    /**
     * @var \DateTime createdAt
     * @ODM\Field(type="date")
     */
    protected $createdAt;

    /**
     * @var User $user
     * @ODM\ReferenceOne(targetDocument="User")
     */
    protected $user;


    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }
}