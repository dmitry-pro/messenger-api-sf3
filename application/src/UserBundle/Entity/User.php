<?php

namespace UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use MessengerBundle\Entity\Dialog;
use JMS\Serializer\Annotation as Serializer;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Groups({"Default"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=100, unique=true)
     *
     * @Serializer\Groups({"Default"})
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="auth_token", type="string", length=255, unique=true)
     *
     * @Serializer\Groups({"Exclude"})
     */
    private $authToken;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MessengerBundle\Entity\Dialog", mappedBy="users", cascade={"persist"})
     *
     * @Serializer\Groups({"Exclude"})
     **/
    private $dialogs;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MessengerBundle\Entity\Message", mappedBy="author")
     *
     * @Serializer\Groups({"Exclude"})
     */
    private $messages;

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
        $this->messages = new ArrayCollection();
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
        $this->dialogs[] = $dialog;
//        $dialog->addUser($this);

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
}
