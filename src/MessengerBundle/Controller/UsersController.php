<?php

namespace MessengerBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use MessengerBundle\Behavior\BearerAuthenticationTrait;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

/**
 * Class UsersController
 *
 * @package MessengerBundle\Controller
 */
class UsersController extends FOSRestController
{
    use BearerAuthenticationTrait;

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUsersAction(Request $request)
    {
        $this->authenticateRequest($request);

        $data = $this->getDoctrine()->getRepository('UserBundle:User')->findAll();
        $view = $this
            ->view($data)
            ->setEngine('json')
        ;

        return $this->handleView($view);
    }

    /**
     * @param int     $id
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUserAction($id, Request $request)
    {
        $this->authenticateRequest($request);

        $data = $this->getDoctrine()->getRepository('UserBundle:User')->find($id);

        if (!$data) {
            throw $this->createNotFoundException();
        }

        $view = $this
            ->view($data)
            ->setEngine('json')
        ;

        return $this->handleView($view);
    }
}
