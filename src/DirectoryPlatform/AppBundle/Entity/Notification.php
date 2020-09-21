<?php

namespace DirectoryPlatform\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="directory_platform_notification")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="DirectoryPlatform\AppBundle\Repository\NotificationRepository")
 */
class Notification
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
     * @ORM\Column(name="text_to_show", type="text", length=255, nullable=true)
     */
    private $textToShow;

    /**
     * @var string
     *
     * @ORM\Column(name="is_read", type="boolean", options={"default" = 0 })
     */
    private $isRead;

    /**
     * @var string
     *
     * @ORM\Column(name="is_archived", type="boolean", nullable=true)
     */
    private $isArchived;

    /**
     * @var string
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="modified", type="datetime", nullable=true)
     */
    private $modified;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notification")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set textToShow
     *
     * @param string $textToShow
     *
     * @return Notification
     */
    public function setTextToShow($textToShow)
    {
        $this->textToShow = $textToShow;
    
        return $this;
    }

    /**
     * Get textToShow
     *
     * @return string
     */
    public function getTextToShow()
    {
        return $this->textToShow;
    }

    /**
     * Set isRead
     *
     * @param string $isRead
     *
     * @return Notification
     */
    public function setIsRead($isRead)
    {
        $this->isRead = $isRead;
    
        return $this;
    }

    /**
     * Get isRead
     *
     * @return boolean
     */
    public function getIsRead()
    {
        return $this->isRead;
    }

    /**
     * Set isArchived
     *
     * @param string $isArchived
     *
     * @return Notification
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;
    
        return $this;
    }

    /**
     * Get isArchived
     *
     * @return string
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * Set isActive
     *
     * @param string $isActive
     *
     * @return Notification
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return string
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created = new \DateTime('now');
        $this->modified = new \DateTime('now');
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->modified = new \DateTime('now');
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Notification
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set modified
     *
     * @param \DateTime $modified
     *
     * @return Notification
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    
        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }
}

