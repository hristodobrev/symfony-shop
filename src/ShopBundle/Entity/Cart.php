<?php

namespace ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 *
 * @ORM\Table(name="carts")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\CartRepository")
 */
class Cart
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
     * @var User
     *
     * @ORM\OneToOne(targetEntity="ShopBundle\Entity\User", inversedBy="cart")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var ArrayCollection|Product[]
     *
     * @ORM\ManyToMany(targetEntity="ShopBundle\Entity\Product", inversedBy="carts")
     * @ORM\JoinTable(name="carts_products",
     *     joinColumns={@ORM\JoinColumn(name="cart_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")}
     * )
     */
    private $products;

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
     * Set user
     *
     * @param string $user
     *
     * @return Cart
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set products
     *
     * @param Product $product
     *
     * @return Cart
     */
    public function addProduct(Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    public function removeProduct(Product $product)
    {
        $filteredProducts = [];
        foreach ($this->products as $p) {
            if (!$p->getId() === $product->getId()) {
                $filteredProducts[] = $p;
            }
        }

        $this->products = $filteredProducts;
    }

    /**
     * Get products
     *
     * @return Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function isProductInCart(Product $product)
    {
        foreach ($this->products as $cartProduct) {
            if ($cartProduct->getId() === $product->getId()) {
                return true;
            }
        }

        return false;
    }

    public function empty()
    {
        foreach ($this->getProducts() as $product) {
            $this->removeProduct($product);
        }
    }
}

