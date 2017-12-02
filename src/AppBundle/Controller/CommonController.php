<?php

namespace AppBundle\Controller;

use AppBundle\Document\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View as FOSView;

class CommonController extends Controller
{
    protected function getDoctrineManager()
    {
        return $this->get('doctrine.odm.mongodb.document_manager');
    }

    /**
     * @param Request $request
     * @return User
     */
    protected function getUserByToken(Request $request)
    {
        $authTokenHeader = $request->headers->get('X-Auth-Token');
        $userByToken = $this->getDoctrineManager()
            ->getRepository('AppBundle:AuthToken')
            ->findOneByValue($authTokenHeader)
            ->getUser();

        return $userByToken;
    }

    protected function getCompanyType($companyTypeId)
    {
        $companyType = $this->getDoctrineManager()
            ->getRepository('AppBundle:CompanyType')
            ->find($companyTypeId);

        if (empty($companyType)) {
            return FOSView::create(
                ['message' => ' Company Type not found'],
                Response::HTTP_NOT_FOUND
            );
        }
        return $companyType;
    }

    protected function getUserRole($userRoleId)
    {
        $userRole = $this->getDoctrineManager()
            ->getRepository('AppBundle:UserRole')
            ->find($userRoleId);

        if (empty($userRole)) {
            return $this->userRoleNotFound();
        }
        return $userRole;
    }



/***************************************************************
****************** ERROR RESPONSE ******************************
****************************************************************/
    /**
     * Not Allowed
     */
    protected function userNotAllowed()
    {
        return FOSView::create(
            ['message' => ' User not allowed'],
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * Role not found
     */
    protected function userRoleNotFound()
    {
        return FOSView::create(
            ['message' => ' UserRole not found'],
            Response::HTTP_NOT_FOUND
        );
    }
}
