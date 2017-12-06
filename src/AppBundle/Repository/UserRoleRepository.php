<?php

namespace AppBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;


/**
 * UserRoleRepository
 *
 */
class UserRoleRepository extends DocumentRepository
{
    /*
     * @param string $userRoleId
     * @return UserRole
     */
    protected function getUserRole($userRoleId)
    {
        $userRole = $this->getDoctrineManager()
            ->getRepository('AppBundle:UserRole')
            ->find($userRoleId);

        if (!$userRole instanceof UserRole) {
            return false;
        }
        return $userRole;
    }
}