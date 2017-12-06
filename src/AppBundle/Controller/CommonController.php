<?php

namespace AppBundle\Controller;

use AppBundle\Document\Access;
use AppBundle\Document\Company;
use AppBundle\Document\Doc;
use AppBundle\Document\Folder;
use AppBundle\Document\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CommonController extends Controller
{
    protected function getDoctrineManager()
    {
        return $this->get('doctrine.odm.mongodb.document_manager');
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
        $user = $this->getDoctrineManager()
            ->getRepository('AppBundle:AuthToken')
            ->getUserByToken($request);

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

        $user = $this->getDoctrineManager()
            ->getRepository('AppBundle:AuthToken')
            ->getUserByToken($request);

        $userRole = $user->getUserRole()->getName();
        /**
         * AccessType
         */
        if (!array_key_exists($userRole, $accessRights))
        {
            return false;
        }
        $accessType = $this->getDoctrineManager()
            ->getRepository('AppBundle:AccessType')
            ->getAccessTypeByName($accessRights[$userRole]);

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

    /**
     * Set Updated Object
     * @var Object, Request $request
     * @return Object
     */
    protected function setUpdated($object, $request)
    {
        $userByToken = $this->getDoctrineManager()
            ->getRepository('AppBundle:AuthToken')
            ->getUserByToken($request);

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
        $userByToken = $this->getDoctrineManager()
            ->getRepository('AppBundle:AuthToken')
            ->getUserByToken($request);

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
        $userByToken = $this->getDoctrineManager()
            ->getRepository('AppBundle:AuthToken')
            ->getUserByToken($request);

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
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException(
            'User not allowed'
        );
    }

    /**
     * User not Found
     */
    protected function userNotFound()
    {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(
            'User not found'
        );
    }

    /**
     * Role not found
     */
    protected function userRoleNotFound()
    {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(
            'UserRole not found'
        );
    }

    /**
     * Company not found
     */
    protected function companyNotFound()
    {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(
            'Company not found'
        );
    }

    /**
     * CompanyType not found
     */
    protected function companyTypeNotFound()
    {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(
            'CompanyType not found'
        );
    }

    /**
     * AccessType not found
     */
    protected function accessTypeNotFound()
    {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(
            'AccessType not found'
        );
    }

    /**
     * Access not found
     */
    protected function accessNotFound()
    {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(
            'Access not found'
        );
    }

    /*
     * Folder not found
     */
    protected function folderNotFound()
    {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(
            'Folder not found'
        );
    }

    /**
     * Doc not found
     */
    protected function docNotFound()
    {
        throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException(
            'Document not found'
        );
    }

    /**
     * Doc extention not permited
     */
    protected function unauthorizedExtention()
    {
        throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException(
            'Extension not permited'
        );
    }

    /**
     * Year format not permited
     */
    protected function unauthorizedDate()
    {
        throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException(
            '', 'Date format is not valid :: expected YYYY'
        );
    }

    /**
     * Year format not permited
     */
    protected function invalidCredentials()
    {
        throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException(
            'Invalid credentials'
        );
    }
}
