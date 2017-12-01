<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\CompanyForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View as FOSView;
use AppBundle\Document\Company;

class CompanyController extends Controller
{
    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"companies"})
     * @Rest\Get("companies")
     */
    public function getCompaniesAction(Request $request)
    {
        $companies = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AppBundle:Company')
            ->findAll();

        return $companies;
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"company"})
     * @Rest\Get("companies/{company_id}")
     */
    public function getCompanyAction(Request $request)
    {
        $company = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AppBundle:Company')
            ->find($request->get('company_id'));

        if (empty($company)) {
            return FOSView::create(
                ['message' => ' Company not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $company;
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"postCompany"})
     * @Rest\Post("companies")
     */
    public function postCompanyAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        /**
         * CompanyType
         */
        $companyType = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AppBundle:CompanyType')
            ->find($request->request->get('companyType'));

        if (empty($companyType)) {
            return FOSView::create(
                ['message' => ' Company Type not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        /**
         * Owner
         */





        $company = new Company();
        $company->setCompanyType($companyType);
        $request->request->remove('companyType');

        $form = $this->createForm(CompanyForm::class, $company);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $dm->persist($company);
            $dm->flush();

            return $company;
        }else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"company"})
     * @Rest\Patch("companies/{company_id}")
     */
    public function patchCompanyAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $company = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AppBundle:Company')
            ->find($request->get('company_id'));

        if (empty($company)) {
            return FOSView::create(
                ['message' => ' Company not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        if (null !== $request->request->get('companyType')) {
            $companyType = $this->get('doctrine.odm.mongodb.document_manager')
                ->getRepository('AppBundle:CompanyType')
                ->find($request->request->get('companyType'));

            if (empty($companyType)) {
                return FOSView::create(
                    ['message' => ' Company Type not found'],
                    Response::HTTP_NOT_FOUND
                );
            }
            $company->setCompanyType($companyType);
            $request->request->remove('companyType');
        }


        $form = $this->createForm(CompanyForm::class, $company);
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            $dm->persist($company);
            $dm->flush();

            return $company;
        } else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("companies/{company_id}")
     */
    public function deleteCompanyAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $company = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AppBundle:Company')
            ->find($request->get('company_id'));

        if (empty($company)) {
            return FOSView::create(
                ['message' => ' Company not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $dm->remove($company);
        $dm->flush();

    }
}
