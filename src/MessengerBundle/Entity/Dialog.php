<?php

namespace MessengerBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;

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
     */
    private $id;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinTable(name="users_dialogs",
     *      joinColumns={@ORM\JoinColumn(name="dialog_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id",
     *      onDelete="CASCADE")}
     *      )
     */
    private $users;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dialog", mappedBy="dialog", cascade={"persist", "remove"})
     */
    private $messages;

    /**
     * Dialog constructor.
     *
     * @param User[]|null $users
     */
    public function __construct($users = null)
    {
        $this->messages = new ArrayCollection();
        $this->users = new ArrayCollection();

        if ($users) {
            foreach ($users as $user) {
                $this->addUser($user);
            }
        }
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
        $user->addDialog($this);
        $this->users[] = $user;

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

