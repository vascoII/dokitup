<?php

namespace AppBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
/**
 * CompanyRepository
 *
 */
class CompanyRepository extends DocumentRepository
{
    /**
     * @param string $companyId
     * @return Company
     */
    protected function getCompany($companyId)
    {
        $company = $this->dm->getRepository('AppBundle:Company')
            ->find($companyId);

        if (!$company instanceof Company) {
            return false;
        }
        return $company;
    }
}