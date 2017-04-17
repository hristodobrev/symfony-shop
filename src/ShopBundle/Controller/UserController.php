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
            $userRole = $this->getDoctrine()->getRepository(Role::class)->findOneBy([
                'name' => 'ROLE_USER'
            ]);
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
     * @Route("/profile/{id}", name="user_profile")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction($id)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security_login');
        }

        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        $isLoggedInUser = $user->getId() === $this->getUser()->getId();

        return $this->render('user/profile.html.twig', [
                'user' => $user,
                'isLoggedInUser' => $isLoggedInUser
            ]
        );
    }
}
