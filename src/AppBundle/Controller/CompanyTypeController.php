<?php

namespace AppBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\Type\CompanyTypeForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View as FOSView;
use AppBundle\Document\CompanyType;

class CompanyTypeController extends CommonController
{
    /**
     *
     *
     * @Rest\View(serializerGroups={"companyType"})
     * @Rest\Get("companyTypes")
     */
    public function getCompanyTypesAction(Request $request)
    {
        $companyTypes = $this->getDoctrineManager()
            ->getRepository('AppBundle:CompanyType')
            ->findAll();

        return $companyTypes;
    }

    /**
     *
     *
     * @Rest\View(serializerGroups={"companyType"})
     * @Rest\Get("companyTypes/{companyType_id}")
     */
    public function getCompanyTypeAction(Request $request)
    {
        $companyType = $this->getDoctrineManager()
            ->getRepository('AppBundle:CompanyType')
            ->find($request->get('companyType_id'));

        if (empty($companyType)) {
            return FOSView::create(
                ['message' => ' Company Type not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $companyType;
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"companyType"})
     * @Rest\Post("companyTypes")
     */
    public function postCompanyTypeAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        $companyType = new CompanyType();

        $form = $this->createForm(CompanyTypeForm::class, $companyType);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $dm->persist($companyType);
            $dm->flush();

            return $companyType;
        }else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"companyType"})
     * @Rest\Patch("companyTypes/{companyType_id}")
     */
    public function patchCompanyTypeAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        $companyType = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AppBundle:CompanyType')
            ->find($request->get('companyType_id'));

        if (empty($companyType)) {
            return FOSView::create(
                ['message' => ' Company Type not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $form = $this->createForm(CompanyTypeForm::class, $companyType);
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            $dm->persist($companyType);
            $dm->flush();

            return $companyType;
        } else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View(serializerGroups={"companyType"})
     * @Rest\Delete("companyTypes/{companyType_id}")
     */
    public function deleteCompanyTypeAction(Request $request)
    {
        die('deleteCompanyTypeAction');
        $dm = $this->getDoctrineManager();

        $companyType = $this->dm->getRepository('AppBundle:CompanyType')
            ->find($request->get('companyType_id'));

        if (empty($companyType)) {
            return $this->companyTypeNotFound();
        }

        $dm->remove($companyType);
        $dm->flush();

    }
}
