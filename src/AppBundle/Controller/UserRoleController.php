<?php

namespace AppBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\Type\UserRoleForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View as FOSView;
use AppBundle\Document\UserRole;

class UserRoleController extends Controller
{
    /**
     *
     *
     * @Rest\View()
     * @Rest\Get("userRoles")
     */
    public function getUserRolesAction(Request $request)
    {
        $userRoles = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AppBundle:UserRole')
            ->findAll();

        return $userRoles;
    }

    /**
     *
     *
     * @Rest\View()
     * @Rest\Get("userRoles/{userRole_id}")
     */
    public function getUserRoleAction(Request $request)
    {
        $userRole = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AppBundle:UserRole')
            ->find($request->get('userRole_id'));

        if (empty($userRole)) {
            return FOSView::create(
                ['message' => ' UserRole not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        return $userRole;
    }

    /**
     *
     *
     * @Rest\View()
     * @Rest\Post("userRoles")
     */
    public function postUserRoleAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        $userRole = new UserRole();

        $form = $this->createForm(UserRoleForm::class, $userRole);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $dm->persist($userRole);
            $dm->flush();

            return $userRole;
        }else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View()
     * @Rest\Patch("userRoles/{userRole_id}")
     */
    public function patchUserRoleAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        $userRole = $dm->getRepository('AppBundle:UserRole')
            ->find($request->get('userRole_id'));

        if (empty($userRole)) {
            return new Response('Pas de role ID');
        }

        $form = $this->createForm(UserRoleForm::class, $userRole);
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            $dm->persist($userRole);
            $dm->flush();

            return $userRole;
        } else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View()
     * @Rest\Delete("userRoles/{userRole_id}")
     */
    public function deleteUserRoleAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $userRole = $dm->getRepository('AppBundle:UserRole')
            ->find($request->get('userRole_id'));

        if (empty($userRole)) {
            return new Response('userRole not found');
        }

        $dm->remove($userRole);
        $dm->flush();

    }
}
