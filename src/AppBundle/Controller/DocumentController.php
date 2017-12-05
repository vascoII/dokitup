<?php

namespace AppBundle\Controller;

use AppBundle\Document\Company;
use AppBundle\Document\Folder;
use AppBundle\Document\Document;
use AppBundle\Form\Type\DocForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Document\Doc;

class DocumentController extends CommonController
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }
}
