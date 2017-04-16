<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\Product;
use ShopBundle\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render('product/viewAll.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/add", name="add_product")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addProduct(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUser($this->getUser());

            $em = $this->getDoctrine()->getManager();

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('view_all_products');
        }

        return $this->render('product/add.html.twig', [
            'productForm' => $form->createView()
        ]);
    }
}
