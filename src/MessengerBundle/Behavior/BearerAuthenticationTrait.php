<?php

namespace MessengerBundle\Behavior;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use UserBundle\Entity\User;

trait BearerAuthenticationTrait
{
    /**
     * @var User|null
     */
    protected $user;

    /**
     * @return User|null
     */
    protected function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    protected function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param Request $request
     */
    protected function authenticateRequest(Request $request)
    {
        $token = $this->parseBearerToken($request);
        if ($token) {
            $user = $this->getDoctrine()->getRepository('UserBundle:User')->findOneBy(['authToken' => $token]);
            if ($user) {
                $this->setUser($user);

                return;
//                echo 'Authorized: ' . $user->getUsername(); die();
            }
        }

        throw new UnauthorizedHttpException('Not Authorized', 401);
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    private function parseBearerToken(Request $request)
    {
        if ($header = $request->headers->get('Authorization')) {
            if (-1 !== strpos($header, 'Bearer') && $parts = preg_split('/[\s]+/', $header)) {
                if (2 == count($parts)) {
                    $token = $parts[1];

                    return $token;
                }
            }
        }

        return null;
    }
}
