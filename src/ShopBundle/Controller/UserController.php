<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ShopBundle\Entity\Cart;
use ShopBundle\Entity\Role;
use ShopBundle\Entity\User;
use ShopBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("user", name="user")
 */
class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
            $user->setPassword($password);
            $user->setCash(100);
            $userRole = $this->getDoctrine()->getRepository(Role::class)->findByName('ROLE_USER');
            $user->addRole($userRole);
            $cart = new Cart();

            $em = $this->getDoctrine()->getManager();

            $em->persist($cart);
            $user->setCart($cart);
            $em->persist($user);
            $cart->setUser($user);
            $em->persist($cart);

            $em->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('user/register.html.twig');
    }

    /**
     * @Route("/profile/{id}", name="user_profile", requirements={"id": "\d+"})
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction($id)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security_login');
        }

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        if ($user === null) {
            $this->addFlash('error', 'Such user does not exist.');
            return $this->redirectToRoute('user_profile', ['id' => $this->getUser()->getId()]);
        }

        $isLoggedInUser = $user->getId() === $this->getUser()->getId();

        $userProducts = $user->getProducts();
        $calculator = $this->get('app.price_calculator');

        return $this->render('user/profile.html.twig', [
                'user' => $user,
                'isLoggedInUser' => $isLoggedInUser,
                'userProducts' => $userProducts,
                'calculator' => $calculator
            ]
        );
    }

    /**
     * @Route("/profile/edit", name="edit_profile")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        $oldPassword = $user->getPassword();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pass = $request->request->get('password');
            $repPass = $request->request->get('rep-password');
            if ($pass || $repPass) {
                if ($repPass != $pass) {
                    $this->addFlash('error', 'The passwords do not match.');
                    return $this->redirectToRoute('edit_profile');
                }

                $userHashedPass = $this->get('security.password_encoder')->encodePassword($user, $pass);
                $user->setPassword($userHashedPass);
            } else {
                $user->setPassword($oldPassword);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('user_profile', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'userForm' => $form->createView(),
            'user' => $user
        ]);
    }
}
