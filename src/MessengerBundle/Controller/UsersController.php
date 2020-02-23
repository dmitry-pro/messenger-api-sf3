<?php

namespace MessengerBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use MessengerBundle\Behavior\BearerAuthenticationTrait;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as FOS;
use Swagger\Annotations as SWG;
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
     * @param Request $request
     *
     * @FOS\Get(path="/users/me")
     *
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getMeAction(Request $request)
    {
        $this->authenticateRequest($request);

        $data = $this->getUser();

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
     * @param int     $userId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUserAction($userId, Request $request)
    {
        $this->authenticateRequest($request);

        $data = $this->getDoctrine()->getRepository('UserBundle:User')->find($userId);

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
     * @param int     $userId
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getUserDialogsAction($userId, Request $request)
    {
        $this->authenticateRequest($request);

        $user = $this->getDoctrine()->getRepository('UserBundle:User')->find($userId);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $dialogs = $this->getDoctrine()->getRepository('UserBundle:User')->getUserDialogs($user);

        $view = $this
            ->view($dialogs)
            ->setEngine('json')
        ;

        return $this->handleView($view);
    }
}
