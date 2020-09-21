<?php

namespace DirectoryPlatform\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmailTemplate
 *
 * @ORM\Table(name="email_template")
 * @ORM\Entity(repositoryClass="DirectoryPlatform\AppBundle\Repository\EmailTemplateRepository")
 */
class EmailTemplate
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
     * @ORM\Column(name="emailKey", type="string", length=255, nullable=true, unique=true)
     */
    private $emailKey;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=255, nullable=true)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", nullable=true)
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="cc", type="string", length=255, nullable=true)
     */
    private $cc;

    /**
     * @var string
     *
     * @ORM\Column(name="bcc", type="string", length=255, nullable=true)
     */
    private $bcc;

    /**
     * @var bool
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;

    /**
     * @var bool
     *
     * @ORM\Column(name="isArchived", type="boolean")
     */
    private $isArchived;


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
     * Set emailKey
     *
     * @param string $emailKey
     *
     * @return EmailTemplate
     */
    public function setEmailKey($emailKey)
    {
        $this->emailKey = $emailKey;
    
        return $this;
    }

    /**
     * Get emailKey
     *
     * @return string
     */
    public function getEmailKey()
    {
        return $this->emailKey;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return EmailTemplate
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    
        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return EmailTemplate
     */
    public function setBody($body)
    {
        $this->body = $body;
    
        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set cc
     *
     * @param string $cc
     *
     * @return EmailTemplate
     */
    public function setCc($cc)
    {
        $this->cc = $cc;
    
        return $this;
    }

    /**
     * Get cc
     *
     * @return string
     */
    public function getCc()
    {
        return $this->cc;
    }

    /**
     * Set bcc
     *
     * @param string $bcc
     *
     * @return EmailTemplate
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;
    
        return $this;
    }

    /**
     * Get bcc
     *
     * @return string
     */
    public function getbcc()
    {
        return $this->bcc;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return EmailTemplate
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isArchived
     *
     * @param boolean $isArchived
     *
     * @return EmailTemplate
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;
    
        return $this;
    }

    /**
     * Get isArchived
     *
     * @return boolean
     */
    public function getIsArchived()
    {
        return $this->isArchived;
    }
}

