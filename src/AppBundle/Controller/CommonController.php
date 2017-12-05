<?php

namespace AppBundle\Controller;

use AppBundle\Document\Access;
use AppBundle\Document\AccessType;
use AppBundle\Document\Company;
use AppBundle\Document\CompanyType;
use AppBundle\Document\Doc;
use AppBundle\Document\Folder;
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

    /**
     * @return AccessType $accessType
     */
    protected function getAccessTypeByName($accessTypeName)
    {
        $accessType = $this->getDoctrineManager()
            ->getRepository('AppBundle:AccessType')
            ->findOneByName($accessTypeName);

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
     * @param string $companyId
     * @return Company
     */
    protected function getCompany($companyId)
    {
        $company = $this->getDoctrineManager()
            ->getRepository('AppBundle:Company')
            ->find($companyId);

        if (!$company instanceof Company) {
            return false;
        }
        return $company;
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
     * @return Folder
     */
    protected function getFolderByCompany($company, $request)
    {
        $boolean = false;
        /**
         * Folder
         */
        $folder = $this->getDoctrineManager()
            ->getRepository('AppBundle:Folder')
            ->find($request->get('folder_id'));

        /**
         * Folder does not exist
         */
        if (!$folder instanceof Folder)
        {
            return $boolean;
        }
        /*
         * Folder exist
         * Check if User has Right
         */
        foreach ($company->getFolders() as $companyFolder) {
            if ($companyFolder->getId() === $folder->getId())
            {
                $boolean = true;
            }
        }

        return ($boolean === true) ? $folder : false;
    }

    /**
     * @param Folder $folder, Request $request
     * @return Folder
     */
    protected function getDocByFolder($folder, $request)
    {
        $boolean = false;
        /**
         * Document
         */
        $document = $this->getDoctrineManager()
            ->getRepository('AppBundle:Doc')
            ->find($request->get('doc_id'));

        /**
         * Document does not exist
         */
        if (!$document instanceof Doc)
        {
            return $boolean;
        }
        /*
         * Document exist
         * Check if User has Right
         */
        foreach ($folder->getDocs() as $folderDocument) {
            if ($folderDocument->getId() === $document->getId())
            {
                $boolean = true;
            }
        }

        return ($boolean === true) ? $document : false;
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
     * @var Access $access, Folder $folder, Company $company, Request $request
     * @return Access $access
     */
    protected function createFolderAccess($access, $folder, $company, $request)
    {
        /**
         * For Dokitup V2, userRole defines AccessType
         */
        $accessRights = ['owner' => 'CRUD', 'accountant' => 'RU'];

        $user = $this->getUserByToken($request);
        $userRole = $user->getUserRole()->getName();
        /**
         * AccessType
         */
        if (!array_key_exists($userRole, $accessRights))
        {
            return false;
        }
        $accessType = $this->getAccessTypeByName($accessRights[$userRole]);
        /**
         * Access
         */
        $access->setAccessType($accessType)
            ->setFolder($folder)
            ->setCreatedBy($user)
            ->addCompany($company);

        return $access;
    }

    protected function accessExist($folder, $accessType)
    {
        $boolean = false;

        foreach ($folder->getAccesses() as $folderAccess) {
            if ($folderAccess->getId() === $accessType->getId())
            {
                $boolean = true;
            }
        }

        return ($boolean === true) ? $accessType : false;

    }

    protected function getAccess($folder, $accessType)
    {
        $array = [
            'accessType' => $accessType,
            'folder' => $folder
        ];
        $access = $this->getDoctrineManager()
            ->getRepository('AppBundle:Access')
            ->findOneBy($array);
        /**
         * Access does not exist
         */
        if (!$access instanceof Access)
        {
            return false;
        }

        return $access;
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
     * Set Updated Object
     * @var Document $document, Request $request
     * @return Document $document
     */
    protected function setUpdatedDoc($document, $request)
    {
        $userByToken = $this->getUserByToken($request);

        switch ($userByToken->getUserRole()->getName()) {
            case 'owner':
                $document->setUpdatedByOwnerAt(new \DateTime());
                $document->setUpdatedByOwner($userByToken);
                $document->setBoolOwner($request->request->get('bool'));
                break;
            case 'accountant':
                $document->setUpdatedByViewerAt(new \DateTime());
                $document->setUpdatedByViewer($userByToken);
                $document->setBoolViewer($request->request->get('bool'));
                break;
            default:
                return false;
        }

        return $document;
    }

    /**
     * Set Created Object
     * @var Object, Request $request
     * @return Object
     */
    protected function setCreated($object, $request)
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
            ['message' => 'User not allowed'],
            Response::HTTP_FORBIDDEN
        );
    }

    /**
     * User not Found
     */
    protected function userNotFound()
    {
        return FOSView::create(
            ['message' => 'User not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Role not found
     */
    protected function userRoleNotFound()
    {
        return FOSView::create(
            ['message' => 'UserRole not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Company not found
     */
    protected function companyNotFound()
    {
        return FOSView::create(
            ['message' => 'Company not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * CompanyType not found
     */
    protected function companyTypeNotFound()
    {
        return FOSView::create(
            ['message' => 'CompanyType not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * AccessType not found
     */
    protected function accessTypeNotFound()
    {
        return FOSView::create(
            ['message' => 'AccessType not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Access not found
     */
    protected function accessNotFound()
    {
        return FOSView::create(
            ['message' => 'Access not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    /*
     * Folder not found
     */
    protected function folderNotFound()
    {
        return FOSView::create(
            ['message' => 'Folder not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Doc not found
     */
    protected function docNotFound()
    {
        return FOSView::create(
            ['message' => 'Document not found'],
            Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Doc extention not permited
     */
    protected function unauthorizedExtention()
    {
        return FOSView::create(
            ['message' => 'Extension not permited'],
            Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Year format not permited
     */
    protected function unauthorizedDate()
    {
        return FOSView::create(
            ['message' => 'Date format is not valid :: expected YYYY'],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
