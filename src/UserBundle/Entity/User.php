<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use MessengerBundle\Entity\Dialog;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=100, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="auth_token", type="string", length=255, unique=true)
     */
    private $authToken;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MessengerBundle\Entity\Dialog", mappedBy="users")
     **/
    private $dialogs;

    /**
     * User constructor.
     *
     * @param string $username
     * @param string $token
     */
    public function __construct($username, $token)
    {
        $this->username = $username;
        $this->authToken = $token;

        $this->dialogs = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set authToken
     *
     * @param string $authToken
     *
     * @return User
     */
    public function setAuthToken($authToken)
    {
        $this->authToken = $authToken;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDialogs()
    {
        return $this->dialogs;
    }

    /**
     * @param Dialog $dialog
     *
     * @return $this
     */
    public function addDialog(Dialog $dialog)
    {
        $dialog->addUser($this);
        $this->dialogs[] = $dialog;

        return $this;
    }

    /**
     * @param Dialog $dialog
     *
     * @return $this
     */
    public function removeDialog(Dialog $dialog)
    {
        $this->dialogs->removeElement($dialog);
        $dialog->removeUser($this);

        return $this;
    }

    /**
     * Get authToken
     *
     * @return string
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }


}
