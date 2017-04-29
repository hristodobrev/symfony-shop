<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\Category;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\Promotion;
use ShopBundle\Form\PromotionType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("promotions", name="promotions")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 *
 */
class PromotionController extends Controller
{
    /**
     * @Route("/all", name="all_promotions")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAllAction()
    {
        $promotions = $this->getDoctrine()
            ->getRepository(Promotion::class)
            ->findAll();

        return $this->render('promotions/viewAll.html.twig', [
            'promotions' => $promotions
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_promotion")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id, Request $request)
    {
        $productRepository = $this->getDoctrine()->getRepository(Product::class);
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);
        $pormotionRepository = $this->getDoctrine()->getRepository(Promotion::class);

        $promotion = $pormotionRepository->find($id);
        $categories = $categoryRepository->findAll();
        $products = $productRepository->findAll();

        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $categoryRepository->find($request->request->get('category'));
            $product = $productRepository->find($request->request->get('product'));

            if ($category) {
                $promotion->setCategory($category);
            } else {
                $promotion->removeCategory();
            }

            if ($product) {
                $promotion->setProduct($product);
            } else {
                $promotion->removeProduct();
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($promotion);
            $em->flush();

            $this->addFlash('success', 'Promotion edited successfully.');
            return $this->redirectToRoute('all_promotions');
        }

        return $this->render('promotions/edit.html.twig', [
            'promotionForm' => $form->createView(),
            'categories' => $categories,
            'products' => $products,
            'promotion' => $promotion
        ]);
    }
}
