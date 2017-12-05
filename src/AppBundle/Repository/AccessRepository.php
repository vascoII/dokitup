<?php

namespace AppBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
/**
 * AccessRepository
 *
 */
class AccessRepository extends DocumentRepository
{
    public function findAccessByFolderCompany($folder, $company)
    {
        $access = $this->dm->createQueryBuilder('AppBundle:Access')
            ->field('folder')->equals($folder)
            ->field('companies')->includesReferenceTo($company)
            ->select('accessType')
            ->getQuery()
            ->getSingleResult();

        return $access;
    }
}