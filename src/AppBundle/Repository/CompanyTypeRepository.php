<?php

namespace AppBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
/**
 * CompanyTypeRepository
 *
 */
class CompanyTypeRepository extends DocumentRepository
{
    /*
     * @param string $companyTypeId
     * @return CompanyType
     */
    protected function getCompanyType($companyTypeId)
    {
        $companyType = $this->dm->getRepository('AppBundle:CompanyType')
            ->find($companyTypeId);

        if (!$companyType instanceof CompanyType) {
            return false;
        }
        return $companyType;
    }
}