<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Document\Company;
use AppBundle\Document\User;
use AppBundle\Document\CompanyType;
use AppBundle\Document\UserRole;

class AdminController extends CommonController
{
    const ADMIN = "admin";
    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"adminUserCompany"})
     * @Rest\Post("adminUserCompany")
     */
    public function createUserCompanyAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        /**
         * Creator
         */
        $creator = $this->getUserByToken($request);
        if ($creator->getUserRole() !== self::ADMIN) {
            return $this->userNotAllowed();
        }
        /**
         * CompanyType
         */
        $companyType = $this->getCompanyType($request->get('companyType'));

        if (!$companyType instanceof CompanyType) {
            return $companyType;
        }
        /**
         * UserRole
         */
        $userRole = $this->getUserRole($request->get('userRole'));
        if (!$userRole instanceof UserRole) {
            return $userRole;
        }

        /**
         * Company
         */
        $company = new Company();
        $company->setName($request->get('companyName'))
            ->setAddress($request->get('companyAddress'))
            ->setCompanyType($companyType)
            ->setCreatedBy($creator);

        $dm->persist($company);

         /*
          * User
          */
        $user = new User();

        $encoder = $this->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $request->get('userPlainPassword'));

        $user->setFirstName($request->get('userFirstName'))
            ->setLastName($request->get('userLastName'))
            ->setEmail($request->get('userEmail'))
            ->setPassword($encoded)
            ->setUserRole($userRole)
            ->setCreatedBy($creator)
            ->addCompany($company);

        $dm->persist($user);

        $company->addUser($user);

        $dm->flush();

        $user->setPlainPassword($request->get('userPlainPassword'));

        return $user;
    }
}
