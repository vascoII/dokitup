<?php

namespace AppBundle\Controller;

use AppBundle\Document\Company;
use AppBundle\Form\Type\UserForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Document\User;

class UserController extends CommonController
{
    /**
     *
     *
     * @Rest\View(serializerGroups={"users"})
     * @Rest\Get("companies/{company_id}/users")
     */
    public function getUsersAction(Request $request)
    {
        /**
         * Company
         */
        $company = $this->getCompanyByUser($request);

        if (!$company instanceof Company) {
            return $this->companyNotFound();
        }

        $users = $company->getUsers();
        return $users;
    }

    /**
     *
     *
     * @Rest\View(serializerGroups={"user"})
     * @Rest\Get("companies/{company_id}/users/{user_id}")
     */
    public function getUserAction(Request $request)
    {
        /**
         * Company
         */
        $company = $this->getCompanyByUser($request);

        if (!$company instanceof Company) {
            return $this->companyNotFound();
        }
        /**
         * User
         */
        $user = $this->getUserByCompany($company, $request);

        if (!$user instanceof User) {
            return $this->userNotFound();
        }

        return $user;

    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"postUser"})
     * @Rest\Post("companies/{company_id}/users")
     */
    public function postUserAction(Request $request)
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
         * UserRole
         */
        $userRole = $this->getUserRole($request->request->get('userRole'));

        if (empty($userRole)) {
            return $this->userRoleNotFound();
        }
        /**
         * User
         */
        $user = new User();
        $user->setUserRole($userRole);
        $request->request->remove('userRole');
        /**
         * Form
         */
        $form = $this->createForm(UserForm::class, $user);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $user = $this->setCreate($user, $request);

            $encoder = $this->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);
            $user->addCompany($company);

            $dm->persist($user);

            $company->addUser($user);
            $dm->persist($company);
            $dm->flush();

            $user->setPlainPassword($request->request->get('plainPassword'));
            return $user;
        }else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_OK, serializerGroups={"postUser"})
     * @Rest\Patch("companies/{company_id}/users/{user_id}")
     */
    public function patchUserAction(Request $request)
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
         * User
         */
        $user = $this->getUserByCompany($company, $request);

        if (!$user instanceof User) {
            return $this->userNotFound();
        }
        /**
         * UserRole
         */
        if (null !== $request->request->get('userRole')) {
            $userRole = $this->getUserRole($request->request->get('userRole'));

            if (empty($userRole)) {
                return $this->userRoleNotFound();
            }
            $user->setUserRole($userRole);
            $request->request->remove('userRole');
        }
        /**
         * Form
         */
        $form = $this->createForm(UserForm::class, $user);
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            /**
             * Patch password
             */
            if (null !== $request->request->get('plainPassword')) {
                $encoder = $this->get('security.password_encoder');
                $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($encoded);
            }
            $user = $this->setUpdated($user, $request);

            $dm->persist($user);
            $dm->flush();

            if (null !== $request->request->get('plainPassword')) {
                $user->setPlainPassword($request->request->get('plainPassword'));
            }
            return $user;
        } else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("companies/{company_id}/users/{user_id}")
     */
    public function deleteUserAction(Request $request)
    {
        return new Response('Action delete User with ID');
    }
}
