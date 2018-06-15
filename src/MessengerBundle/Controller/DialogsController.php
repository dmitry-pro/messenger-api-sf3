<?php

namespace MessengerBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use MessengerBundle\Behavior\BearerAuthenticationTrait;
use MessengerBundle\Entity\Message;
use MessengerBundle\Repository\DialogRepository;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as FOS;
use Swagger\Annotations as SWG;

/**
 * Class UsersController
 *
 * @package MessengerBundle\Controller
 */
class DialogsController extends FOSRestController
{
    use BearerAuthenticationTrait;

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDialogsAction(Request $request)
    {
        $this->authenticateRequest($request);

        $data = $this->getDoctrine()->getRepository('MessengerBundle:Dialog')->findAll();
        $view = $this
            ->view($data)
            ->setEngine('json')
        ;

        return $this->handleView($view);
    }

    /**
     * @param int     $dialogId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDialogAction($dialogId, Request $request)
    {
        $this->authenticateRequest($request);

        $data = $this->getDoctrine()->getRepository('MessengerBundle:Dialog')->find($dialogId);

        if (!$data) {
            throw $this->createNotFoundException();
        }

        $view = $this
            ->view($data)
            ->setEngine('json')
        ;

        return $this->handleView($view);
    }

    /**
     * @param int     $dialogId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getDialogMessagesAction($dialogId, Request $request)
    {
        $this->authenticateRequest($request);

        $dialog = $this->getDoctrine()->getRepository('MessengerBundle:Dialog')->find($dialogId);

        if (!$dialog) {
            throw $this->createNotFoundException();
        }

        $messages = $this->getDoctrine()->getRepository('MessengerBundle:Dialog')->getDialogMessages($dialog);

        $view = $this
            ->view($messages)
            ->setEngine('json')
        ;

        return $this->handleView($view);
    }

    /**
     * @param int     $dialogId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @FOS\Post(path="/dialogs/{dialogId}/messages/create")
     *
     * @SWG\Post(
     *      @SWG\Parameter(
     *          name="message",
     *          in="body",
     *          required=false,
     *          type="object",
     *          schema=@SWG\Schema(
     *             required={"text"},
     *             @SWG\Property(property="text", type="string", example="Hello world")
     *          )
     *      ),
     *
     *      @SWG\Response(
     *          response=200,
     *          description="success",
     *          schema = @SWG\Schema(
     *             required={"text"},
     *             @SWG\Property(property="text", type="string", example="Message Text")
     *          )
     *      )
     * )
     *
     */
    public function createDialogMessageAction($dialogId, Request $request)
    {
        $this->authenticateRequest($request);

        $text = $request->request->get('text');

        $dialogRepo = $this->getDoctrine()->getRepository('MessengerBundle:Dialog');

        $dialog = $dialogRepo->find($dialogId);

        if ($dialog) {
            $author = $this->getUser();

            $message = new Message();
            $message
                ->setAuthor($author)
                ->setText($text);
            ;

            $newMessage = $dialogRepo->writeToDialog($dialog, $message);

            $view = $this
                ->view($newMessage)
                ->setEngine('json');

            return $this->handleView($view);
        } else {
            throw $this->createNotFoundException();
        }
    }
}
