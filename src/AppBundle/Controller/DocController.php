<?php

namespace AppBundle\Controller;

use AppBundle\Document\Company;
use AppBundle\Document\Folder;
use AppBundle\Document\Doc;
use AppBundle\Form\Type\DocForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;

class DocController extends CommonController
{
    const OWNER = 'owner';
    const ACCOUNTANT = 'accountant';
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
         * Access to Folder from Company
         */
        $access = $dm->getRepository('AppBundle:Access')
            ->findAccessByFolderCompany($folder, $company);

        /**
         * Doc
         */
        $doc = $this->getDocByFolder($folder, $request);
        if (!$doc instanceof Doc) {
            return $this->docNotFound();
        }
        $doc->getFolder()->getAccesses()->clear();
        $doc->getFolder()->getAccesses()->add($access);
        return $doc;
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
        $selfUser = $this->getUserByToken($request);
        /**
         * Pour Dokitup v2 un user de userRole accountant ne peux pas uploader de doc
         */
        if ($selfUser->getUserRole()->getName() === self::ACCOUNTANT)
        {
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
}
