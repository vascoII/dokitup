<?php

namespace AppBundle\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Symfony\Component\HttpFoundation\Request;

/**
 * AuthTokenRepository
 *
 */
class AuthTokenRepository extends DocumentRepository
{
    /**
     * @param Request $request
     * @return User
     */
    public function getUserByToken(Request $request)
    {
        $authTokenHeader = $request->headers->get('X-Auth-Token');
        $userByToken = $this->dm->getRepository('AppBundle:AuthToken')
            ->findOneByValue($authTokenHeader)
            ->getUser();

        return $userByToken;
    }
}