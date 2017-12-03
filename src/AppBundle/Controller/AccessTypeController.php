<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\AccessTypeForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Document\AccessType;

class AccessTypeController extends CommonController
{
    /**
     *
     *
     * @Rest\View(serializerGroups={"accessType"})
     * @Rest\Get("accessTypes")
     */
    public function getAccessTypesAction(Request $request)
    {
        $accessTypes = $this->getAccessTypes();
        return $accessTypes;
    }

    /**
     *
     *
     * @Rest\View(serializerGroups={"accessType"})
     * @Rest\Get("accessTypes/{accessType_id}")
     */
    public function getAccessTypeAction(Request $request)
    {
        $accessType = $this->getAccessType($request->get('accessType_id'));

        if (!$accessType instanceof AccessType)
        {
            return $this->accessTypeNotFound();
        }

        return $accessType;
    }

    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"accessType"})
     * @Rest\Post("accessTypes")
     */
    public function postAccessTypeAction(Request $request)
    {
        $dm = $this->getDoctrineManager();

        $accessType = new AccessType();

        $form = $this->createForm(AccessTypeForm::class, $accessType);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $accessType = $this->setCreate($accessType, $request);
            $dm->persist($accessType);
            $dm->flush();

            return $accessType;
        }else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View(serializerGroups={"accessType"})
     * @Rest\Patch("accessTypes/{accessType_id}")
     */
    public function patchAccessTypeAction(Request $request)
    {
        $dm = $this->getDocumentManager();

        $accessType = $this->getAccessType($request->get('accessType_id'));

        if ($accessType instanceof AccessType)
        {
            return $this->accessTypeNotFound();
        }

        $form = $this->createForm(AccessTypeForm::class, $accessType);
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            $dm->persist($accessType);
            $dm->flush();

            return $accessType;
        } else {
            return $form;
        }
    }

    /**
     *
     *
     * @Rest\View(serializerGroups={"accessType"})
     * @Rest\Delete("accessTypes/{accessType_id}")
     */
    public function deleteCompanyTypeAction(Request $request)
    {

    }
}
