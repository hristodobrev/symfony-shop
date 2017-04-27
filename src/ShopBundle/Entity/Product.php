<?php

namespace ShopBundle\Entity;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\ProductRepository")
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
     * @Assert\NotBlank()
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=0.01, minMessage="Price must be more than 0.01")
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     */
    private $price;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\User", inversedBy="products")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    private $category;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min=0, minMessage="Quantity must be more than 0")
     * @Required()
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", options={ "default"="CURRENT_TIMESTAMP" })
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime", options={ "default"="CURRENT_TIMESTAMP" })
     */
    private $dateUpdated;

    /**
     * @var bool
     *
     * @ORM\Column(name="available", type="boolean", options={ "default"=true })
     */
    private $available;

    /**
     * @var ArrayCollection|Cart[]
     *
     * @ORM\ManyToMany(targetEntity="ShopBundle\Entity\Cart", mappedBy="products")
     */
    private $carts;

    /**
     * @var Promotion[]
     *
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\Promotion", mappedBy="product")
     */
    private $promotions;

    public function __construct()
    {
        $this->dateCreated = new \DateTime();
        $this->dateUpdated = new \DateTime();
        $this->available = true;
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
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Set price
     *
     * @param string $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Product
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     *
     * @return Product
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @return \DateTime
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * @param \DateTime $dateUpdated
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;
    }

    /**
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->available;
    }

    /**
     * @param boolean $available
     */
    public function setAvailable($available)
    {
        $this->available = $available;
    }

    /**
     * @return ArrayCollection|Cart[]
     */
    public function getCarts()
    {
        return $this->carts;
    }

    /**
     * @param ArrayCollection|Cart[] $carts
     *
     * @return Product
     */
    public function setCarts($carts)
    {
        $this->carts = $carts;

        return $this;
    }

    /**
     * @return Product[]
     */
    public function getRelatedProducts()
    {
        $categoryProducts = $this->getCategory()->getProducts();

        if (count($categoryProducts) < 4) {
            return $categoryProducts;
        }

        $products = [];
        while (count($products) < 3) {
            $randIndex = rand(0, count($categoryProducts) - 1);
            if (in_array($categoryProducts[$randIndex], $products) || $this->getId() === $categoryProducts[$randIndex]->getId()) {
                continue;
            }

            $products[] = $categoryProducts[$randIndex];
        }

        return $products;
    }

    /**
     * @return Promotion[]
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * @param Promotion $promotion
     */
    public function addPromotion(Promotion $promotion)
    {
       $this->promotions[] = $promotion;
    }
}

