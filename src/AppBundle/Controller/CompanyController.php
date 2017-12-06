<?php

namespace AppBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Document\CompanyType;
use AppBundle\Form\Type\CompanyForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Document\Company;

class CompanyController extends CommonController
{
    /**
     * @ApiDoc(
     *    description="Company Document List based on User Token",
     *    output= { "class"=Company::class, "collection"=true, "groups"={"companies"} },
     *    statusCodes = {
     *        200 = "Company Document List"
     *    },
     *    responseMap={
     *         200 = {"class"=Company::class, "groups"={"companies"}}
     *    }
     * )
     *
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"companies"})
     * @Rest\Get("companies")
     */
    public function getCompaniesAction(Request $request)
    {
        /**
         * SelfUser
         */
        $selfUser = $this->getDoctrineManager()
            ->getRepository('AppBundle:AuthToken')
            ->getUserByToken($request);

        return $selfUser->getCompanies();
    }

    /**
     * @ApiDoc(
     *    description="Company Document",
     *    output= { "class"=Company::class, "collection"=false, "groups"={"company"} },
     *    statusCodes = {
     *        200 = "Company Document",
     *        404 = "Company not found"
     *    },
     *    responseMap={
     *         200 = {"class"=Company::class, "groups"={"company"}},
     *         404 = {"class"=Company::class}
     *    }
     * )
     *
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"company"})
     * @Rest\Get("companies/{company_id}")
     */
    public function getCompanyAction(Request $request)
    {
        $company = $this->getCompanyByUser($request);

        if (!$company instanceof Company) {
            return $this->companyNotFound();
        }

        return $company;
    }

    /**
     * @ApiDoc(
     *    description="Create Company Document",
     *    input={"class"=Company::class, "name"=""},
     *    statusCodes = {
     *        201 = "Company Document"
     *    },
     *    responseMap={
     *         201 = {"class"=Company::class, "groups"={"postCompany"}}
     *    }
     * )
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"postCompany"})
     * @Rest\Post("companies")
     */
    public function postCompanyAction(Request $request)
    {
        $dm = $this->getDoctrineManager();
        /**
         * CompanyType
         */
        $companyType = $this->getDoctrineManager()
            ->getRepository('AppBundle:CompanyType')
            ->getCompanyType($request->request->get('companyType'));

        if (!$companyType instanceof CompanyType) {
            return $this->companyTypeNotFound();
        }
        /**
         * Company
         */
        $company = new Company();
        $company->setCompanyType($companyType);
        $request->request->remove('companyType');
        /**
         * Form
         */
        $form = $this->createForm(CompanyForm::class, $company);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $company = $this->setCreated($company, $request);

            $dm->persist($company);
            $dm->flush();

            return $company;
        }else {
            return $form;
        }
    }

    /**
     * @ApiDoc(
     *    description="Update Company Document",
     *    input={"class"=Company::class, "name"=""},
     *    statusCodes = {
     *        200 = "Company Document",
     *        404 = "Company not found"
     *    },
     *    responseMap={
     *         200 = {"class"=Company::class, "groups"={"company"}},
     *         404 = {"class"=Company::class}
     *    }
     * )
     *
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"company"})
     * @Rest\Patch("companies/{company_id}")
     */
    public function patchCompanyAction(Request $request)
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
         * CompanyType
         */
        if (null !== $request->request->get('companyType')) {
            $companyType = $this->getDoctrineManager()
                ->getRepository('AppBundle:CompanyType')
                ->getCompanyType($request->request->get('companyType'));

            if (!$companyType instanceof CompanyType) {
                return $this->companyTypeNotFound();
            }
            $company->setCompanyType($companyType);
            $request->request->remove('companyType');
        }


        $form = $this->createForm(CompanyForm::class, $company);
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            $company = $this->setUpdated($company, $request);

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
        die('deleteCompanyAction');
        $dm = $this->getDoctrineManager();
        /**
         * Company
         */
        $company = $this->getCompanyByUser($request);

        if (!$company instanceof Company) {
            return $this->companyNotFound();
        }

        $dm->remove($company);
        $dm->flush();

    }
}
