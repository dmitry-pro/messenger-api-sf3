<?php

namespace MessengerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;
use JMS\Serializer\Annotation as Serializer;

/**
 * Dialog
 *
 * @ORM\Table(name="dialogs")
 * @ORM\Entity(repositoryClass="MessengerBundle\Repository\DialogRepository")
 */
class Dialog
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
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", inversedBy="dialogs", cascade={"persist"})
     * @ORM\JoinTable(name="users_dialogs",
     *      joinColumns={@ORM\JoinColumn(name="dialog_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id",
     *      onDelete="CASCADE")}
     *      )
     *
     * @Serializer\Groups({"Default"})
     * @Serializer\MaxDepth(1)
     */
    private $users;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Message", mappedBy="dialog", cascade={"persist", "remove"})
     *
     * @Serializer\Groups({"Exclude"})
     * @Serializer\MaxDepth(1)
     */
    private $messages;

    /**
     * Dialog constructor.
     *
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->users = new ArrayCollection();
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
     * Add user
     *
     * @param User $user
     *
     * @return Dialog
     */
    public function addUser(User $user)
    {
        $this->users[] = $user;
        $user->addDialog($this);

        return $this;
    }

    /**
     * @param User $user
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
        $user->removeDialog($this);
    }

    /**
     * Get users
     *
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set messages
     *
     * @param ArrayCollection $messages
     *
     * @return Dialog
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;

        return $this;
    }

    /**
     * Add message
     *
     * @param Message $message
     *
     * @return Dialog
     */
    public function addMessage(Message $message)
    {
        $this->messages[] = $message;
        $message->setDialog($this);

        return $this;
    }

    /**
     * Get messages
     *
     * @return ArrayCollection
     */
    public function getMessages()
    {
        return $this->messages;
    }
}

