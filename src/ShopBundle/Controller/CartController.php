<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ShopBundle\Entity\Cart;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("cart", name="cart")
 */
class CartController extends Controller
{
    /**
     * @Route("/view", name="view_cart")
     */
    public function viewCartAction()
    {
        /** @var Cart $cart */
        $cart = $this->getUser()->getCart();

        return $this->render('cart/view.html.twig', ['cart' => $cart]);
    }

    /**
     * @Route("/checkout", name="cart_check_out")
     */
    public function checkOutCartAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var Cart $cart */
        $cart = $user->getCart();

        $priceCalculator = $this->get('app.price_calculator');
        $totalPrice = $priceCalculator->getCartTotalPrice($cart);
        $userCash = $user->getCash();
        if ($userCash < $totalPrice) {
            $this->addFlash('error', 'You don\'t have enough cash to check out the cart. Remove some products first.');
            return $this->redirectToRoute('view_cart');
        }

        // Decrease user cash
        $user->setCash($user->getCash() - $totalPrice);

        // Increase seller's cash and decrease product quantity
        foreach ($cart->getProducts() as $product) {
            if ($product->getQuantity() < 1) {
                $this->addFlash('error', 'Product is not in stock.');
                continue;
            }

            $product->getUser()->setCash(
                $product->getUser()->getCash() +
                $priceCalculator->calculatePrice($product)
            );
            $product->setQuantity($product->getQuantity() - 1);
        }

        $cart->empty();
        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Cart checked out successfully.');
        return $this->redirectToRoute('view_all_products');
    }

    /**
     * @Route("/add/{productId}", name="add_product_to_cart")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addToCartAction($productId)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($productId);

        if ($product === null) {
            $this->addFlash('error', 'Product does not exist.');
            return $this->redirectToRoute('view_all_products');
        }

        /** @var User $user */
        $user = $this->getUser();
        /** @var Cart $cart */
        $cart = $user->getCart();

        if ($cart->isProductInCart($product)) {
            $this->addFlash('error', 'This product is already in your cart.');
            return $this->redirectToRoute('view_all_products');
        }
        if ($product->getQuantity() === 0) {
            $this->addFlash('error', 'This product is not in stock.');
            return $this->redirectToRoute('view_all_products');
        }
        if ($product->getUser()->getId() === $this->getUser()->getId()) {
            $this->addFlash('error', 'Cannot add your own product to your cart.');
            return $this->redirectToRoute('view_all_products');
        }

        $cart->addProduct($product);

        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();

        $this->addFlash('success', 'Product added to cart successfully.');
        return $this->redirectToRoute('view_all_products');
    }

    /**
     * @Route("/remove/{productId}", name="remove_product_from_cart")
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeFromCartAction($productId, Request $request)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($productId);

        if ($product === null) {
            $this->addFlash('error', 'Product does not exist.');
            return $this->redirect($request->headers->get('referer'));
        }

        /** @var User $user */
        $user = $this->getUser();
        /** @var Cart $cart */
        $cart = $user->getCart();

        if (!$cart->isProductInCart($product)) {
            $this->addFlash('error', 'This product is not in your cart.');
            return $this->redirect($request->headers->get('referer'));
        }

        $cart->removeProduct($product);

        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();

        $this->addFlash('warning', 'Product removed from cart.');
        return $this->redirect($request->headers->get('referer'));
    }
}
