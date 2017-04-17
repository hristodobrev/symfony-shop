<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Category;
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
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render('product/viewAll.html.twig', [
            'products' => $products,
            'categories' => $categories
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

            return $this->redirectToRoute('view_all_products');
        }

        return $this->render('product/add.html.twig', [
            'productForm' => $form->createView(),
            'categories' => $categories
        ]);
    }
}
