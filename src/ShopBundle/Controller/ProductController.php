<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Cart;
use ShopBundle\Entity\Category;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use ShopBundle\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("product", name="product")
 */
class ProductController extends Controller
{
    /**
     * @Route("/all", name="view_all_products")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAllAction()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        $products = $this->getDoctrine()->getRepository(Product::class)->findAllAvailable();

        return $this->render('product/viewAll.html.twig', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/view/{id}", name="view_product", requirements={"id": "\d+"})
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function view($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if ($product === null) {
            $this->addFlash('error', 'Product does not exist.');
            return $this->redirectToRoute('view_all_products');
        }

        return $this->render('product/view.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/category/{id}", name="view_products_by_category")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewByCategoryAction($id)
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $categoryRepository->findAll();
        $category = $categoryRepository->find($id);

        $products = $category->getProducts();

        return $this->render('product/viewByCategory.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'currentCategoryId' => $id
        ]);
    }

    /**
     * @Route("/add", name="add_product")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addProduct(Request $request)
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUser($this->getUser());
            $category = $categoryRepository->find($request->request->get('category'));
            $product->setCategory($category);

            $em = $this->getDoctrine()->getManager();

            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Product added successfully.');

            return $this->redirectToRoute('view_all_products');
        }

        return $this->render('product/add.html.twig', [
            'productForm' => $form->createView(),
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_product")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editProduct($id, Request $request)
    {
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $categories = $categoryRepository->findAll();

        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);

        if ($product == null) {
            $this->addFlash('error', 'Product does not exist.');
            $this->redirectToRoute('view_all_products');
        }

        if ($this->getUser()->getId() != $product->getUser()->getId() &&
            !$this->getUser()->isAdmin() &&
            !$this->getUser()->isEditor()
        ) {
            $this->addFlash('error', 'You cannot edit other users\' posts.');
            return $this->redirectToRoute('view_all_products');
        }

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUser($this->getUser());
            $category = $categoryRepository->find($request->request->get('category'));
            $product->setCategory($category);
            $product->setDateUpdated(new \DateTime());

            $em = $this->getDoctrine()->getManager();

            $em->persist($product);
            $em->flush();

            $this->addFlash('info', 'Product edited successfully.');
            return $this->redirectToRoute('view_all_products');
        }

        return $this->render('product/edit.html.twig', [
            'productForm' => $form->createView(),
            'categories' => $categories,
            'product' => $product
        ]);
    }

    /**
     * @Route("/addToCart/{productId}", name="add_product_to_cart")
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

        $cart->addProduct($product);

        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();

        $this->addFlash('success', 'Product added to cart successfully.');
        return $this->redirectToRoute('view_all_products');
    }
}
