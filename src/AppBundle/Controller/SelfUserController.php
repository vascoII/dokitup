<?php

namespace AppBundle\Controller;

use AppBundle\Document\UserRole;
use AppBundle\Form\Type\UserForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View as FOSView;
use AppBundle\Document\User;

class SelfUserController extends CommonController
{
    /**
     *
     *
     * @Rest\View(serializerGroups={"self"})
     * @Rest\Get("self")
     */
    public function getSelfAction(Request $request)
    {
        /**
         * SelfUser
         */
        $selfUser = $this->getUserByToken($request);

        return $selfUser;
    }

    /**
     *
     *
     * @Rest\View(serializerGroups={"self"})
     * @Rest\Patch("self")
     */
    public function patchSelfAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        /**
         * SelfUser
         */
        $selfUser = $this->getUserByToken($request);

        /**
         * Patch Role
         */
        if (null !== $request->request->get('userRole'))
        {
            $userRole = $this->getUserRole($request->request->get('userRole'));
            if (!$userRole instanceof UserRole) {
                return $this->userRoleNotFound();
            }
            $selfUser->setUserRole($userRole);
            $request->request->remove('userRole');
        }

        $form = $this->createForm(UserForm::class, $selfUser);
        $form->submit($request->request->all(), false);

        if ($form->isValid())
        {
            /**
             * Patch password
             */
            if (null !== $request->request->get('plainPassword')) {
                $encoder = $this->get('security.password_encoder');
                $encoded = $encoder->encodePassword($selfUser, $request->get('userPlainPassword'));
                $selfUser->setPassword($encoded);
            }
            $selfUser->setUpdatedAt(new \DateTime());

            $dm->persist($selfUser);
            $dm->flush();

            return $selfUser;
        } else {
            return $form;
        }
    }

}
