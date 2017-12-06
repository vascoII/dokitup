<?php

namespace AppBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
/**
 * AccessTypeRepository
 *
 */
class AccessTypeRepository extends DocumentRepository
{
    /**
     * @return Collection AccessType $accessTypes
     */
    protected function getAccessTypes()
    {
        $accessTypes = $this->dm->getRepository('AppBundle:AccessType')
            ->findAll();

        return $accessTypes;
    }

    /**
     * @return AccessType $accessType
     */
    protected function getAccessType($accessTypeId)
    {
        $accessType = $this->dm->getRepository('AppBundle:AccessType')
            ->find($accessTypeId);

        return $accessType;
    }

    /**
     * @return AccessType $accessType
     */
    protected function getAccessTypeByName($accessTypeName)
    {
        $accessType = $this->dm->getRepository('AppBundle:AccessType')
            ->findOneByName($accessTypeName);

        return $accessType;
    }
}