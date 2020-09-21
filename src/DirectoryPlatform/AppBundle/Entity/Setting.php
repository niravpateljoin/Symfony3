<?php

namespace DirectoryPlatform\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Setting
 *
 * @ORM\Table(name="directory_platform_setting")
 * @ORM\Entity(repositoryClass="DirectoryPlatform\AppBundle\Repository\SettingRepository")
 */

class Setting
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
     * @ORM\Column(name="key_val", type="string", length=255, unique=true)
     */
    private $keyVal;
    
    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;


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
     * Set value
     *
     * @param string $value
     *
     * @return Setting
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set keyVal
     *
     * @param string $keyVal
     *
     * @return Setting
     */
    public function setKeyVal($keyVal)
    {
        $this->keyVal = $keyVal;
    
        return $this;
    }

    /**
     * Get keyVal
     *
     * @return string
     */
    public function getKeyVal()
    {
        return $this->keyVal;
    }
}

