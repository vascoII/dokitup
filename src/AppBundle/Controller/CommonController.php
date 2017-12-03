<?php

namespace AppBundle\Controller;

use AppBundle\Document\Access;
use AppBundle\Document\AccessType;
use AppBundle\Document\Company;
use AppBundle\Document\CompanyType;
use AppBundle\Document\User;
use AppBundle\Document\UserRole;
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

    /*
     * @param string $companyTypeId
     * @return CompanyType
     */
    protected function getCompanyType($companyTypeId)
    {
        $companyType = $this->getDoctrineManager()
            ->getRepository('AppBundle:CompanyType')
            ->find($companyTypeId);

        if (!$companyType instanceof CompanyType) {
            return false;
        }
        return $companyType;
    }

    /**
     * @return Collection AccessType $accessTypes
     */
    protected function getAccessTypes()
    {
        $accessTypes = $this->getDoctrineManager()
            ->getRepository('AppBundle:AccessType')
            ->findAll();

        return $accessTypes;
    }

    /**
     * @return AccessType $accessType
     */
    protected function getAccessType($accessTypeId)
    {
        $accessType = $this->getDoctrineManager()
            ->getRepository('AppBundle:AccessType')
            ->find($accessTypeId);

        return $accessType;
    }

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

    /**
     * @param Request $request
     * @return Company
     */
    protected function getCompanyByUser($request)
    {
        $boolean = false;
        /*
         * User
         */
        $user = $this->getUserByToken($request);
        /**
         * Company
         */
        $company = $this->getDoctrineManager()
            ->getRepository('AppBundle:Company')
            ->find($request->get('company_id'));

        /**
         * Company does not exist
         */
        if (!$company instanceof Company)
        {
            return $boolean;
        }
        /*
         * Company exist
         * Check if User has Right
         */
        foreach ($user->getCompanies() as $userCompany) {
            if ($userCompany->getId() === $company->getId())
            {
                $boolean = true;
            }
        }

        return ($boolean === true) ? $company : false;
    }

    /**
     * @param Company $company, Request $request
     * @return User
     */
    protected function getUserByCompany($company, $request)
    {
        $boolean = false;
        /**
         * User
         */
        $user = $this->getDoctrineManager()
            ->getRepository('AppBundle:User')
            ->find($request->get('user_id'));
        /**
         * User does not exist
         */
        if (!$user instanceof User)
        {
            return $boolean;
        }
        /*
         * User exist
         * Check if connectedUser has Right
         */
        foreach ($company->getUsers() as $companyUser) {
            if ($companyUser->getId() === $user->getId())
            {
                $boolean = true;
            }
        }

        return ($boolean === true) ? $user : false;

    }

    /**
     * Create Access
     * @var Folder $folder, Request $request
     * @return Access $access
     */
    protected function createFolderAccess($folder, $request)
    {
        $userRole = $this->getUserByToke($request)->getUserRole();
        /**
         * For Dokitup V2, the userRole define AccessType
         */

    }

    /**
     * Set Updated Object
     * @var Object, Request $request
     * @return Object
     */
    protected function setUpdated($object, $request)
    {
        $userByToken = $this->getUserByToken($request);

        $object->setUpdatedAt(new \DateTime());
        $object->setUpdatedBy($userByToken);

        return $object;
    }

    /**
     * Set Created Object
     * @var Object, Request $request
     * @return Object
     */
    protected function setCreate($object, $request)
    {
        $userByToken = $this->getUserByToken($request);

        $object->setCreatedBy($userByToken);

        return $object;
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
     * User not Found
     */
    protected function userNotFound()
    {
        return FOSView::create(
            ['message' => ' User not found'],
            Response::HTTP_NOT_FOUND
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

    /**
     * Company not found
     */
    protected function companyNotFound()
    {
        return FOSView::create(
            ['message' => ' Company not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * CompanyType not found
     */
    protected function companyTypeNotFound()
    {
        return FOSView::create(
            ['message' => ' CompanyType not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * AccessType not found
     */
    protected function accessTypeNotFound()
    {
        return FOSView::create(
            ['message' => ' AccessType not found'],
            Response::HTTP_NOT_FOUND
        );
    }
}
