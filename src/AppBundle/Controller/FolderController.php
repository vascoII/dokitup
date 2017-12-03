<?php

namespace AppBundle\Controller;

use AppBundle\Document\Access;
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
        $company = $this->getCompanyByFolder($request);

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
            $folder = $this->setCreate($folder, $request);
            $dm->persist($folder);

            $company->addUser($folder);
            $dm->persist($company);
            /**
             * Access
             */
            $access = new Access();
            $access = $this->createFolderAccess($folder, $request);



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
     * @Rest\Patch("companies/{company_id}/users/{user_id}")
     */
    public function patchFolderAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        /**
         * Company
         */
        $company = $this->getCompanyByFolder($request);

        if (!$company instanceof Company) {
            return $this->companyNotFound();
        }
        /**
         * Folder
         */
        $Folder = $this->getFolderByCompany($company, $request);

        if (!$Folder instanceof Folder) {
            return $this->FolderNotFound();
        }
        /**
         * FolderRole
         */
        if (null !== $request->request->get('FolderRole')) {
            $FolderRole = $this->getFolderRole($request->request->get('FolderRole'));

            if (empty($FolderRole)) {
                return $this->FolderRoleNotFound();
            }
            $Folder->setFolderRole($FolderRole);
            $request->request->remove('FolderRole');
        }
        /**
         * Form
         */
        $form = $this->createForm(FolderForm::class, $Folder);
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            /**
             * Patch password
             */
            if (null !== $request->request->get('plainPassword')) {
                $encoder = $this->get('security.password_encoder');
                $encoded = $encoder->encodePassword($Folder, $Folder->getPlainPassword());
                $Folder->setPassword($encoded);
            }
            $Folder = $this->setUpdated($Folder, $request);

            $dm->persist($Folder);
            $dm->flush();

            if (null !== $request->request->get('plainPassword')) {
                $Folder->setPlainPassword($request->request->get('plainPassword'));
            }
            return $Folder;
        } else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("companies/{company_id}/Folders/{Folder_id}")
     */
    public function deleteFolderAction(Request $request)
    {
        return new Response('Action delete Folder with ID');
    }
}
