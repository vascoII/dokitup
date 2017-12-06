<?php

namespace AppBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Document\Company;
use AppBundle\Document\User;
use AppBundle\Document\CompanyType;
use AppBundle\Document\UserRole;

class AdminController extends CommonController
{
    /**
     * @ApiDoc(
     *    description="Create Company and User for the ADMINAccessType Document List",
     *    output= { "class"=AccessType::class, "collection"=true, "groups"={"accessType"} },
     *    statusCodes = {
     *        200 = "AccessType Document List"
     *    },
     *    responseMap={
     *         200 = {"class"=AccessType::class, "groups"={"accessType"}}
     *    }
     * )
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"adminUserCompany"})
     * @Rest\Post("adminUserCompany")
     */
    public function createUserCompanyAction(Request $request)
    {
        $dm = $this->getDoctrineManager();

        /**
         * Creator
         */
        $creator = $this->getDoctrineManager()
            ->getRepository('AppBundle:AuthToken')
            ->getUserByToken($request);

        if (
            $creator->getUserRole()->getName() !==
            $this->getParameter('admin')
        ) {
            return $this->userNotAllowed();
        }
        /**
         * CompanyType
         */
        $companyType = $this->getDoctrineManager()
			->getRepository('AppBundle:CompanyType')
			->getCompanyType($request->get('companyType'));

        if (!$companyType instanceof CompanyType) {
            return $companyType;
        }
        /**
         * UserRole
         */
        $userRole = $this->getDoctrineManager()
            ->getRepository('AppBundle:UserRole')
            ->getUserRole($request->get('userRole'));

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
