<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View as FOSView;
use AppBundle\Form\Type\CredentialsType;
use AppBundle\Document\AuthToken;
use AppBundle\Document\Credentials;

class AuthTokenController extends Controller
{
    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Post("/auth-tokens")
     */
    public function postAuthTokensAction(Request $request)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);

        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $form;
        }

        $user = $dm->getRepository('AppBundle:User')
            ->findOneByEmail($credentials->getLogin());

        if (!$user) {
            return FOSView::create(
                ['message' => ' User not found'],
                Response::HTTP_NOT_FOUND
            );
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

    private function invalidCredentials()
    {
        return FOSView::create(
            ['message' => ' Invalid credentials'],
            Response::HTTP_BAD_REQUEST
        );
    }
}