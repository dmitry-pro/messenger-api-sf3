<?php

namespace MessengerBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use MessengerBundle\Behavior\BearerAuthenticationTrait;
use MessengerBundle\Entity\Message;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations as FOS;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UsersController
 *
 * @package MessengerBundle\Controller
 */
class MessagesController extends FOSRestController
{
    use BearerAuthenticationTrait;

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMessagesAction(Request $request)
    {
        $this->authenticateRequest($request);

        $data = $this->getDoctrine()->getRepository('MessengerBundle:Message')->findAll();
        $view = $this
            ->view($data)
            ->setEngine('json');

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @FOS\Post(path="/messages/create")
     *
     * @SWG\Post(
     *      @SWG\Parameter(
     *          name="message",
     *          in="body",
     *          required=false,
     *          type="object",
     *          schema=@SWG\Schema(
     *             required={"text", "username"},
     *             @SWG\Property(property="text", type="string", example="Hello world"),
     *             @SWG\Property(property="username", type="string", example="zebra")
     *          )
     *      ),
     *
     *      @SWG\Response(
     *          response=200,
     *          description="success",
     *          schema = @SWG\Schema(
     *             required={"text", "username"},
     *             @SWG\Property(property="text", type="string", example="Message Text"),
     *             @SWG\Property(property="dialog", type="string", example="Dialog ID")
     *          )
     *      )
     * )
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createMessageAction(Request $request)
    {
        $this->authenticateRequest($request);

        $text = $request->request->get('text');
        $username = $request->request->get('username');

        if ($username && $user = $this->getDoctrine()->getRepository('UserBundle:User')->findByUsername($username)) {
            $author = $this->getUser();

            if ($author == $user) {
                throw new HttpException(400, 'Dialogs with the same user are disabled in this version.');
            }

            $message = new Message();
            $message
                ->setAuthor($author)
                ->setText($text);
            ;

            $newMessage = $this->getDoctrine()->getRepository('MessengerBundle:Message')->messageUser(
                $author,
                $user,
                $message
            );

            $view = $this
                ->view($newMessage)
                ->setStatusCode(201)
                ->setEngine('json');

            return $this->handleView($view);
        } else {
            throw $this->createNotFoundException();
        }
    }
}
