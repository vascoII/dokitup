<?php

namespace AppBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Document\Company;
use AppBundle\Document\Folder;
use AppBundle\Document\Doc;
use AppBundle\Form\Type\DocForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class DocController extends CommonController
{
    /**
     *
     *
     * @Rest\View(serializerGroups={"docs"})
     * @Rest\Get("companies/{company_id}/folders/{folder_id}/docs")
     */
    public function getDocsAction(Request $request)
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
         * Access to Folder from Company
         */
        $access = $dm->getRepository('AppBundle:Access')
            ->findAccessByFolderCompany($folder, $company);

        $documentContainer = [];
        foreach ($folder->getDocs() as $folderDoc)
        {
            $folderDoc->getFolder()->getAccesses()->clear();
            $folderDoc->getFolder()->getAccesses()->add($access);
            $documentContainer[] = $folderDoc;
        }

        return $documentContainer;
    }

    /**
     *
     *
     * @Rest\View(serializerGroups={"doc"})
     * @Rest\Get("companies/{company_id}/folders/{folder_id}/docs/{doc_id}")
     */
    public function getDocAction(Request $request)
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
         * Doc
         */
        $doc = $this->getDocByFolder($folder, $request);
        if (!$doc instanceof Doc) {
            return $this->docNotFound();
        }

        $pdfPath = $this->getParameter('uploaded_dir').
            $doc->getFileUrl().$doc->getFileName();

        return $this->file($pdfPath);

    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"postDoc"})
     * @Rest\Post("companies/{company_id}/folders/{folder_id}/docs")
     */
    public function postDocAction(Request $request)
    {
        $dm = $this->getDoctrineManager();
        /**
         * SelfUser
         */
        $selfUser = $this->getDoctrineManager()
            ->getRepository('AppBundle:AuthToken')
            ->getUserByToken($request);
        /**
         * Pour Dokitup v2 un user de userRole accountant ne peux pas uploader de doc
         */
        if (
            $selfUser->getUserRole()->getName() ===
            $this->getParameter('accountant')
        ) {
            return $this->userNotAllowed();
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
         * Document
         */
        $file = $request->files->get('file');
        $year = intval($request->request->get('year'));
        if (2010 > $year || $year > 2038)
        {
            return $this->unauthorizedDate();
        }

        if (!in_array(
                $file->guessExtension(),
                $this->getParameter('uploaded_files_extension')
            )
        ) {
            return $this->unauthorizedExtention();
        }

        $originalName = $file->getClientOriginalName();
        $targetDir = str_replace(
            ' ',
            '_',
            $this->getParameter('uploaded_files').'/'.$company->getName().'/'.$year
        );
        $file->move($this->getParameter('uploaded_dir').$targetDir, $originalName);

        $document = new Doc();

        $document = $this->setCreated($document, $request);
        $document->setFileName($originalName)
            ->setFileUrl($targetDir.'/')
            ->setFolder($folder)
            ->setYear(new \DateTime($year.'-01-01'));
        $dm->persist($document);

        $folder->addDoc($document);
        $dm->persist($folder);

        $dm->flush();

        return $document;
    }

    /**
     * @Rest\View(serializerGroups={"doc"})
     * @Rest\Patch("companies/{company_id}/folders/{folder_id}/docs/{doc_id}")
     */
    public function patchDocAction(Request $request)
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
         * Doc
         */
        $doc = $this->getDocByFolder($folder, $request);
        if (!$doc instanceof Doc) {
            return $this->docNotFound();
        }

        $doc = $this->setUpdatedDoc($doc, $request);
        if (!$doc instanceof Doc)
        {
            return $this->userNotAllowed();
        }
        /**
         * Access to Folder from Company
         */
        $access = $dm->getRepository('AppBundle:Access')
            ->findAccessByFolderCompany($folder, $company);

        $dm->persist($doc);
        $dm->flush();

        $doc->getFolder()->getAccesses()->clear();
        $doc->getFolder()->getAccesses()->add($access);
        return $doc;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"doc"})
     * @Rest\Delete("companies/{company_id}/folders/{folder_id}/docs/{doc_id}")
     */
    public function deleteDocAction(Request $request)
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
         * Doc
         */
        $doc = $this->getDocByFolder($folder, $request);
        if (!$doc instanceof Doc) {
            return $this->docNotFound();
        }

        $doc = $this->setUpdatedDoc($doc, $request);
        if (!$doc instanceof Doc)
        {
            return $this->userNotAllowed();
        }
        /**
         * Access to Folder from Company
         */
        $access = $dm->getRepository('AppBundle:Access')
            ->findAccessByFolderCompany($folder, $company);
        if (
            $access->getAccessType()->getName() ===
            $this->getParameter('crud')
        ) {
            $dm->remove($doc);
            $dm->flush();
        } else {
            return $this->userNotAllowed();
        }


    }
}
