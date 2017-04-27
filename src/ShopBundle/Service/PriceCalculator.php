<?php

namespace ShopBundle\Service;

use Doctrine\ORM\EntityManager;
use ShopBundle\Entity\Cart;
use ShopBundle\Entity\Category;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\Promotion;

class PriceCalculator
{
    private $categoryPromotions;
    private $productPromotions;
    private $biggestCommonPromotion = 0;
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getCartTotalPrice(Cart $cart)
    {
        $products = $cart->getProducts();
        $totalPrice = 0;
        foreach ($products as $product) {
            $totalPrice += $this->calculatePrice($product);
        }

        return $totalPrice;
    }

    public function calculatePrice(Product $product)
    {
        $this->initPromotions();

        $initialPrice = $product->getPrice();

        $discount = $this->biggestCommonPromotion;
        if (array_key_exists($product->getId(), $this->productPromotions)) {
            if ($this->productPromotions[$product->getId()] > $discount) {
                $discount = $this->productPromotions[$product->getId()];
            }
        }
        if (array_key_exists($product->getCategory()->getId(), $this->categoryPromotions)) {
            if ($this->categoryPromotions[$product->getCategory()->getId()] > $discount) {
                $discount = $this->categoryPromotions[$product->getCategory()->getId()];
            }
        }

        $price = $initialPrice - $initialPrice * ($discount / 100);
        return $price;
    }

    private function initPromotions()
    {
        $promotionsRepo = $this->em->getRepository(Promotion::class);
        $productsRepo = $this->em->getRepository(Product::class);
        $categoriesRepo = $this->em->getRepository(Category::class);

        if (!isset($this->categoryPromotions)) {
            $this->categoryPromotions = [];
            foreach ($categoriesRepo->findAll() as $category) {
                if (!array_key_exists($category->getId(), $this->categoryPromotions)) {
                    $this->categoryPromotions[$category->getId()] = 0;
                }
                /** @var Promotion $currentPromotion */
                $currentPromotion = $promotionsRepo->getBiggestCategoryPromotion($category);
                if ($currentPromotion === null) {
                    continue;
                }

                if ($currentPromotion->getDiscount() > $this->categoryPromotions[$category->getId()]) {
                    $this->categoryPromotions[$category->getId()] = $currentPromotion->getDiscount();
                }
            }
        }

        if (!isset($this->productPromotions)) {
            $this->productPromotions = [];
            foreach ($productsRepo->findAll() as $product) {
                if (!array_key_exists($product->getId(), $this->productPromotions)) {
                    $this->productPromotions[$product->getId()] = 0;
                }
                /** @var Promotion $currentPromotion */
                $currentPromotion = $promotionsRepo->getBiggestProductPromotion($product);
                if ($currentPromotion === null) {
                    continue;
                }

                if ($currentPromotion->getDiscount() > $this->productPromotions[$product->getId()]) {
                    $this->productPromotions[$product->getId()] = $currentPromotion->getDiscount();
                }
            }
        }

        if ($this->biggestCommonPromotion == 0) {
            $this->biggestCommonPromotion = $promotionsRepo->getBiggestCommonPromotion()->getDiscount();
        }
    }
}