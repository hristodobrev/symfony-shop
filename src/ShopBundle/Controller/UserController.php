<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ShopBundle\Entity\Role;
use ShopBundle\Entity\User;
use ShopBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('user/register.html.twig');
    }

    /**
     * @Route("/check", name="user_check")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function checkUser(Request $request)
    {
        if ($request->request->all()) {
            //dump($request->request->get('_username'));
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
                'username' => $request->request->get('_username')
            ]);

            if($this->get('security.password_encoder')->isPasswordValid($user,
                $request->request->get('_password'))) {
                echo 'Valid';
                exit;
            } else {
                echo 'Invalid';
                exit;
            }

            dump($user);
            exit;
        }

        return $this->render('user/check.html.twig', ['request' => $request]);
    }
}
