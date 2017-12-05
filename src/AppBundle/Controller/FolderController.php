<?php

namespace AppBundle\Controller;

use AppBundle\Document\Access;
use AppBundle\Document\AccessType;
use AppBundle\Document\Company;
use AppBundle\Form\Type\FolderForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Document\Folder;

class FolderController extends CommonController
{
    /**
     *
     *
     * @Rest\View(serializerGroups={"folders"})
     * @Rest\Get("companies/{company_id}/folders")
     */
    public function getFoldersAction(Request $request)
    {
        /**
         * Company
         */
        $company = $this->getCompanyByUser($request);

        if (!$company instanceof Company) {
            return $this->companyNotFound();
        }

        $folders = $company->getFolders();
        return $folders;
    }

    /**
     *
     *
     * @Rest\View(serializerGroups={"folder"})
     * @Rest\Get("companies/{company_id}/folders/{folder_id}")
     */
    public function getFolderAction(Request $request)
    {
        /**
         * Company
         */
        $company = $this->getCompanyByUser($request);

        if (!$company instanceof Company) {
            return $this->companyNotFound();
        }
        /**
         * Folder
         */
        $folder = $this->getFolderByCompany($company, $request);

        if (!$folder instanceof Folder) {
            return $this->folderNotFound();
        }

        return $folder;

    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"postFolder"})
     * @Rest\Post("companies/{company_id}/folders")
     */
    public function postFolderAction(Request $request)
    {
        $dm = $this->getDoctrineManager();
        /**
         * Company
         */
        $company = $this->getCompanyByUser($request);

        if (!$company instanceof Company) {
            return $this->companyNotFound();
        }
        /**
         * Folder
         */
        $folder = new Folder();
        /**
         * Form
         */
        $form = $this->createForm(FolderForm::class, $folder);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            /**
             * Access
             */
            $access = new Access();
            $access = $this->createFolderAccess($access, $folder, $company, $request);

            if (!$access instanceof Access)
            {
                return $this->userNotAllowed();
            }
            $dm->persist($access);

            $folder = $this->setCreated($folder, $request);
            $folder->addAccess($access);
            $folder->addCompany($company);
            $dm->persist($folder);

            $company = $this->setUpdated($company, $request);
            $company->addFolder($folder);
            $dm->persist($company);

            $dm->flush();

            return $folder;
        }else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"postFolder"})
     * @Rest\Patch("companies/{company_id}/folders/{folder_id}")
     */
    public function patchFolderAction(Request $request)
    {
        $dm = $this->getDoctrineManager();
        /**
         * Company
         */
        $company = $this->getCompanyByUser($request);

        if (!$company instanceof Company) {
            return $this->companyNotFound();
        }
        /**
         * Folder
         */
        $folder = $this->getFolderByCompany($company, $request);

        if (!$folder instanceof Folder) {
            return $this->folderNotFound();
        }
        /**
         * Form
         */
        $form = $this->createForm(FolderForm::class, $folder);
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            $folder = $this->setUpdated($folder, $request);
            $dm->persist($folder);
            $dm->flush();

            return $folder;
        } else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"folder"})
     * @Rest\Patch("companies/{company_id}/folders/{folder_id}/addCompanyAccess/{addCompany_id}")
     */
    public function addFolderAccessToCompanyAction(Request $request)
    {
        $dm = $this->getDoctrineManager();
        /**
         * AccessType
         */
        $accessType = $this->getAccessType($request->request->get('accessType'));
        if (!$accessType instanceof AccessType) {
            return $this->accessTypeNotFound();
        }
        /**
         * Company
         */
        $company = $this->getCompanyByUser($request);

        if (!$company instanceof Company) {
            return $this->companyNotFound();
        }
        /**
         * Folder
         */
        $folder = $this->getFolderByCompany($company, $request);

        if (!$folder instanceof Folder) {
            return $this->folderNotFound();
        }
        /**
         * CompanyAdded
         */
        $companyAdded = $this->getCompany($request->get('addCompany_id'));
        if (!$companyAdded instanceof Company) {
            return $this->companyNotFound();
        }
        /**
         * Access
         */
        $access = $this->accessExist($folder, $accessType);
        if ($access instanceof Access) { //Access already exist :: Add $companyAdded
            $access = $this->setUpdated($access, $request);
            $access->addCompany($companyAdded);
            $dm->persist($access);

        } else { //Access does not exist :: Create new Access
            /**
             * Access
             */
            $access = new Access();
            $access->setAccessType($accessType)
                ->setFolder($folder)
                ->addCompany($companyAdded);
            $access = $this->setCreated($access, $request);

            $dm->persist($access);
        }

        $folder = $this->setUpdated($folder, $request);
        $folder->addCompany($companyAdded);
        $folder->addAccess($access);
        $dm->persist($folder);

        $companyAdded = $this->setUpdated($companyAdded, $request);
        $companyAdded->addFolder($folder);
        $dm->persist($companyAdded);

        $dm->flush();

        return $folder;
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"folder"})
     * @Rest\Delete("companies/{company_id}/folders/{folder_id}/removeCompanyAccess/{removeCompany_id}")
     */
    public function removeFolderFromCompanyAction(Request $request)
    {
        $dm = $this->getDoctrineManager();
        /**
         * AccessType
         */
        $accessType = $this->getAccessType($request->request->get('accessType'));
        if (!$accessType instanceof AccessType) {
            return $this->accessTypeNotFound();
        }
        /**
         * Company
         */
        $company = $this->getCompanyByUser($request);

        if (!$company instanceof Company) {
            return $this->companyNotFound();
        }
        /**
         * Folder
         */
        $folder = $this->getFolderByCompany($company, $request);

        if (!$folder instanceof Folder) {
            return $this->folderNotFound();
        }
        /**
         * CompanyRemouved
         */
        $companyRemouved = $this->getCompany($request->get('removeCompany_id'));
        if (!$companyRemouved instanceof Company) {
            return $this->companyNotFound();
        }
        /**
         * Access
         */
        $access = $this->getAccess($folder, $accessType);
        if ($access instanceof Access)
        {
            $access->removeCompany($companyRemouved);
            $access = $this->setUpdated($access, $request);
            if (count($access->getCompanies()) == 0)
            {
                $folder->removeAccess($access);
                $folder = $this->setUpdated($folder, $request);
                $dm->remove($access);
            } else {
                $dm->persist($access);
            }
        } else {
            return $this->accessNotFound();
        }

        $folder->removeCompany($companyRemouved);
        $dm->persist($folder);

        $companyRemouved = $this->setUpdated($companyRemouved, $request);
        $companyRemouved->removeFolder($folder);
        $dm->persist($companyRemouved);

        $dm->flush();

        return $folder;
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("companies/{company_id}/Folders/{Folder_id}")
     */
    public function deleteFolderAction(Request $request)
    {
        die('Action delete Folder with ID');
    }
}
