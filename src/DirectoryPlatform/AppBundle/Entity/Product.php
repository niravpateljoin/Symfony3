<?php

namespace DirectoryPlatform\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Validator\Constraints as Assert;

use Vich\UploaderBundle\Mapping\Annotation as Vich;
/**
 * Product
 *
 * @ORM\Table(name="directory_platform_product")
 * @ORM\Entity(repositoryClass="DirectoryPlatform\AppBundle\Repository\ProductRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Product
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
     * @ORM\Column(name="product_name", type="string", length=255)
     */
    private $productName;

    /**
     * @var string
     *
     * @ORM\Column(length=255, unique=true)
     */
    private $sku;
	
	/**
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="products")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $user;
	
	/**
	 * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
	 * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="SET NULL")
	 */
	private $category;


    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean")
     */
    private $enabled;

    /**
     * @var int
     *
     * @ORM\Column(name="inventory", type="integer")
     */
    private $inventory;

    /**
     * @var float
     *
     * @ORM\Column(name="percentage_thc", type="float", nullable=true)
     */
    private $percentageThc;

    /**
     * @var float
     *
     * @ORM\Column(name="percentage_thca", type="float", nullable=true)
     */
    private $percentageThca;

    /**
     * @var float
     *
     * @ORM\Column(name="percentage_cbd", type="float", nullable=true)
     */
    private $percentageCbd;

    /**
     * @var float
     *
     * @ORM\Column(name="percentage_cba", type="float", nullable=true)
     */
    private $percentageCba;

    /**
     * @var float
     *
     * @ORM\Column(name="percentage_cbn", type="float", nullable=true)
     */
    private $percentageCbn;

    /**
     * @var float
     *
     * @ORM\Column(name="price_gram", type="float", nullable=true)
     */
    private $priceGram;

    /**
     * @var float
     *
     * @ORM\Column(name="price2_gram", type="float", nullable=true)
     */
    private $price2Gram;

    /**
     * @var float
     *
     * @ORM\Column(name="price3_5_gram", type="float", nullable=true)
     */
    private $price35Gram;

    /**
     * @var float
     *
     * @ORM\Column(name="price4_gram", type="float", nullable=true)
     */
    private $price4Gram;

    /**
     * @var float
     *
     * @ORM\Column(name="price7_gram", type="float", nullable=true)
     */
    private $price7Gram;

    /**
     * @var float
     *
     * @ORM\Column(name="price14_gram", type="float", nullable=true)
     */
    private $price14Gram;

    /**
     * @var float
     *
     * @ORM\Column(name="price28_gram", type="float", nullable=true)
     */
    private $price28Gram;
	
	/**
	 * @ORM\Column(length=255, unique=true)
	 */
	protected $slug;
	
	/**
		* @ORM\Column(name="created", type="datetime")
	 */
	private $created;

	/**
	 * @ORM\Column(name="modified", type="datetime", nullable=true)
	 */
	private $modified;
	
	public function __construct()
	{
		$this->products = new ArrayCollection();
	}

	public function __toString()
	{
		return $this->name;
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
	
	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}
	
	/**
		* @return mixed
	*/
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param mixed $user
	 */
	public function setUser($user)
	{
		$this->user = $user;
	}
	public function getVendor() {
		if ($this->getUser()) {
			return $this->getUser()->getDisplayName();
		}

	}
    /**
     * Set productName
     *
     * @param string $productName
     *
     * @return Product
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set sku
     *
     * @param string $sku
     *
     * @return Product
     */
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

	
	
    /**
	 * @return mixed
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * @param mixed $category
	 */
	public function setCategory($category)
	{
		$this->category = $category;
	}

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Product
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set inventory
     *
     * @param integer $inventory
     *
     * @return Product
     */
    public function setInventory($inventory)
    {
        $this->inventory = $inventory;

        return $this;
    }

    /**
     * Get inventory
     *
     * @return int
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * Set percentageThc
     *
     * @param integer $percentageThc
     *
     * @return Product
     */
    public function setPercentageThc($percentageThc)
    {
        $this->percentageThc = $percentageThc;

        return $this;
    }

    /**
     * Get percentageThc
     *
     * @return int
     */
    public function getPercentageThc()
    {
        return $this->percentageThc;
    }

    /**
     * Set percentageThca
     *
     * @param integer $percentageThca
     *
     * @return Product
     */
    public function setPercentageThca($percentageThca)
    {
        $this->percentageThca = $percentageThca;

        return $this;
    }

    /**
     * Get percentageThca
     *
     * @return int
     */
    public function getPercentageThca()
    {
        return $this->percentageThca;
    }

    /**
     * Set percentageCbd
     *
     * @param string $percentageCbd
     *
     * @return Product
     */
    public function setPercentageCbd($percentageCbd)
    {
        $this->percentageCbd = $percentageCbd;

        return $this;
    }

    /**
     * Get percentageCbd
     *
     * @return string
     */
    public function getPercentageCbd()
    {
        return $this->percentageCbd;
    }

    /**
     * Set percentageCba
     *
     * @param float $percentageCba
     *
     * @return Product
     */
    public function setPercentageCba($percentageCba)
    {
        $this->percentageCba = $percentageCba;

        return $this;
    }

    /**
     * Get percentageCba
     *
     * @return float
     */
    public function getPercentageCba()
    {
        return $this->percentageCba;
    }

    /**
     * Set percentageCbn
     *
     * @param float $percentageCbn
     *
     * @return Product
     */
    public function setPercentageCbn($percentageCbn)
    {
        $this->percentageCbn = $percentageCbn;

        return $this;
    }

    /**
     * Get percentageCbn
     *
     * @return float
     */
    public function getPercentageCbn()
    {
        return $this->percentageCbn;
    }

    /**
     * Set priceGram
     *
     * @param float $priceGram
     *
     * @return Product
     */
    public function setPriceGram($priceGram)
    {
        $this->priceGram = $priceGram;

        return $this;
    }

    /**
     * Get priceGram
     *
     * @return float
     */
    public function getPriceGram()
    {
        return $this->priceGram;
    }

    /**
     * Set price2Gram
     *
     * @param float $price2Gram
     *
     * @return Product
     */
    public function setPrice2Gram($price2Gram)
    {
        $this->price2Gram = $price2Gram;

        return $this;
    }

    /**
     * Get price2Gram
     *
     * @return float
     */
    public function getPrice2Gram()
    {
        return $this->price2Gram;
    }

    /**
     * Set price35Gram
     *
     * @param float $price35Gram
     *
     * @return Product
     */
    public function setPrice35Gram($price35Gram)
    {
        $this->price35Gram = $price35Gram;

        return $this;
    }

    /**
     * Get price35Gram
     *
     * @return float
     */
    public function getPrice35Gram()
    {
        return $this->price35Gram;
    }

    /**
     * Set price4Gram
     *
     * @param float $price4Gram
     *
     * @return Product
     */
    public function setPrice4Gram($price4Gram)
    {
        $this->price4Gram = $price4Gram;

        return $this;
    }

    /**
     * Get price4Gram
     *
     * @return float
     */
    public function getPrice4Gram()
    {
        return $this->price4Gram;
    }

    /**
     * Set price7Gram
     *
     * @param float $price7Gram
     *
     * @return Product
     */
    public function setPrice7Gram($price7Gram)
    {
        $this->price7Gram = $price7Gram;

        return $this;
    }

    /**
     * Get price7Gram
     *
     * @return float
     */
    public function getPrice7Gram()
    {
        return $this->price7Gram;
    }

    /**
     * Set price14Gram
     *
     * @param float $price14Gram
     *
     * @return Product
     */
    public function setPrice14Gram($price14Gram)
    {
        $this->price14Gram = $price14Gram;

        return $this;
    }

    /**
     * Get price14Gram
     *
     * @return float
     */
    public function getPrice14Gram()
    {
        return $this->price14Gram;
    }

    /**
     * Set price28Gram
     *
     * @param float $price28Gram
     *
     * @return Product
     */
    public function setPrice28Gram($price28Gram)
    {
        $this->price28Gram = $price28Gram;

        return $this;
    }

    /**
     * Get price28Gram
     *
     * @return float
     */
    public function getPrice28Gram()
    {
        return $this->price28Gram;
    }

	/**
	 * @return mixed
	 */
	public function getSlug()
	{
		return $this->slug;
	}

	/**
	 * @param mixed $slug
	 */
	public function setSlug($slug)
	{
		$this->slug = $slug;
	}
	
}

