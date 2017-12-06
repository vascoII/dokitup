<?php

namespace AppBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Document\UserRole;
use AppBundle\Form\Type\UserForm;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;

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
        $selfUser = $this->getDoctrineManager()->getRepository('AppBundle:AuthToken')->getUserByToken($request);
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
        $selfUser = $this->getDoctrineManager()->getRepository('AppBundle:AuthToken')->getUserByToken($request);

        /**
         * Patch userRole
         */
        if (null !== $request->request->get('userRole'))
        {
            $userRole = $this->getDoctrineManager()
                ->getRepository('AppBundle:UserRole')
                ->getUserRole($request->request->get('userRole'));
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
                $encoded = $encoder->encodePassword($selfUser, $request->get('plainPassword'));
                $selfUser->setPassword($encoded);
                $selfUser->setPlainPassword($request->get('plainPassword'));
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
