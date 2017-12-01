<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\UserForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View as FOSView;
use AppBundle\Document\User;

class UserController extends Controller
{
    /**
     *
     *
     * @Rest\View(serializerGroups={"users"})
     * @Rest\Get("companies/{company_id}/users")
     */
    public function getUsersAction(Request $request)
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
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        $company = $dm->getRepository('AppBundle:Company')
            ->find($request->get('company_id'));

        if (empty($company)) {
            return FOSView::create(
                ['message' => ' Company not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $user = $dm->getRepository('AppBundle:User')
            ->find($request->get('user_id'));

        if (empty($user)) {
            return FOSView::create(
                ['message' => ' User not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $user;

    }

    /**
     *
     *
     * @Rest\View(serializerGroups={"postUser"})
     * @Rest\Post("companies/{company_id}/users")
     */
    public function postUserAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        $company = $dm->getRepository('AppBundle:Company')
            ->find($request->get('company_id'));

        if (empty($company)) {
            return FOSView::create(
                ['message' => ' Company not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        /**
         * UserRole
         */
        $userRole = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AppBundle:UserRole')
            ->find($request->request->get('userRole'));

        if (empty($userRole)) {
            return FOSView::create(
                ['message' => ' UserRole not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $user = new User();
        $user->setUserRole($userRole);
        $request->request->remove('userRole');

        $form = $this->createForm(UserForm::class, $user);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $encoder = $this->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($encoded);
            $user->addCompany($company);

            $dm->persist($user);

            $company->addUser($user);
            $dm->persist($company);
            $dm->flush();

            return $user;
        }else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View(serializerGroups={"postUser"})
     * @Rest\Patch("companies/{company_id}/users/{user_id}")
     */
    public function patchUserAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        $company = $dm->getRepository('AppBundle:Company')
            ->find($request->get('company_id'));

        if (empty($company)) {
            return FOSView::create(
                ['message' => ' Company not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $user = $dm->getRepository('AppBundle:User')
            ->find($request->get('user_id'));

        if (empty($user)) {
            return FOSView::create(
                ['message' => 'User not found'],
                Response::HTTP_NOT_FOUND
            );
        }

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
            $user->setUpdatedAt(\DateTime());

            $dm->persist($user);
            $dm->flush();

            return $user;
        } else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View()
     * @Rest\Delete("companies/{company_id}/users/{user_id}")
     */
    public function deleteUserAction(Request $request)
    {
        return new Response('Action delete User with ID');
    }
}
