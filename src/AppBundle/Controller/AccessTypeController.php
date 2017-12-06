<?php

namespace AppBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Form\Type\AccessTypeForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Document\AccessType;

class AccessTypeController extends CommonController
{
    /**
     * @ApiDoc(
     *    description="AccessType Document List",
     *    output= { "class"=AccessType::class, "collection"=true, "groups"={"accessType"} },
     *    statusCodes = {
     *        200 = "AccessType Document List"
     *    },
     *    responseMap={
     *         200 = {"class"=AccessType::class, "groups"={"accessType"}}
     *    }
     * )
     *
     * @Rest\View(serializerGroups={"accessType"})
     * @Rest\Get("accessTypes")
     */
    public function getAccessTypesAction(Request $request)
    {
        $accessTypes = $this->getDoctrineManager()
            ->getRepository('AppBundle:AccessType')
            ->getAccessTypes();

        return $accessTypes;
    }

    /**
     * @ApiDoc(
     *    description="AccessType Document",
     *    output= { "class"=AccessType::class, "collection"=false, "groups"={"accessType"} },
     *    statusCodes = {
     *        200 = "AccessType Document",
     *        404 = "AccessType not found"
     *    },
     *    responseMap={
     *         200 = {"class"=AccessType::class, "groups"={"accessType"}},
     *         404 = {"class"=AccessType::class}
     *    }
     * )
     *
     * @Rest\View(serializerGroups={"accessType"})
     * @Rest\Get("accessTypes/{accessType_id}")
     */
    public function getAccessTypeAction(Request $request)
    {
        $accessType = $this->getDoctrineManager()
            ->getRepository('AppBundle:AccessType')
            ->getAccessType($request->get('accessType_id'));

        if (!$accessType instanceof AccessType)
        {
            return $this->accessTypeNotFound();
        }

        return $accessType;
    }

    /**
     * @ApiDoc(
     *    description="Create AccessType Document",
     *    input={"class"=AccessType::class, "name"=""},
     *    statusCodes = {
     *        201 = "AccessType Document"
     *    },
     *    responseMap={
     *         201 = {"class"=AccessType::class, "groups"={"accessType"}}
     *    }
     * )
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
            $accessType = $this->setCreated($accessType, $request);
            $dm->persist($accessType);
            $dm->flush();

            return $accessType;
        }else {
            return $form;
        }
    }

}
