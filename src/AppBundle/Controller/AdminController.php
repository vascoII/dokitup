<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View as FOSView;
use AppBundle\Document\Company;
use AppBundle\Document\User;

class AdminController extends Controller
{
    /**
     *
     *
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"postCompany"})
     * @Rest\Post("adminUserCompany")
     */
    public function createUserCompanyAction(Request $request)
    {

    }
}
