<?php
namespace AppBundle\Controller;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View as FOSView;
use AppBundle\Form\Type\CredentialsType;
use AppBundle\Document\AuthToken;
use AppBundle\Document\Credentials;

class AuthTokenController extends CommonController
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Post("/auth-tokens")
     */
    public function postAuthTokensAction(Request $request)
    {
        $dm = $this->getDoctrineManager();

        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $user = $dm->getRepository('AppBundle:User')
            ->findOneByEmail($credentials->getLogin());

        if (!$user) {
            $this->userNotFound();
        }

        $encoder = $this->get('security.password_encoder');
        $isPasswordValid = $encoder->isPasswordValid($user, $credentials->getPassword());

        if (!$isPasswordValid) {
            return $this->invalidCredentials();
        }

        $authToken = new AuthToken();
        $authToken->setValue(base64_encode(random_bytes(50)));
        $authToken->setCreatedAt(new \DateTime('now'));
        $authToken->setUser($user);

        $dm->persist($authToken);
        $dm->flush();

        return $authToken;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/auth-tokens/{id}")
     */
    public function removeAuthTokenAction(Request $request)
    {
        $dm = $this->getDoctrineManager();
        $authToken = $dm->getRepository('AppBundle:AuthToken')
            ->find($request->get('id'));
        /* @var $authToken AuthToken */

        $connectedUser = $this->get('security.token_storage')->getToken()->getUser();

        if ($authToken && $authToken->getUser()->getId() === $connectedUser->getId()) {
            $dm->remove($authToken);
            $dm->flush();
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException();
        }
    }
}