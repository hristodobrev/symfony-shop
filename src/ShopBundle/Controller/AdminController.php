<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ShopBundle\Entity\Role;
use ShopBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("admin")
 *
 * Class AdminController
 * @package ShopBundle\Controller
 */
class AdminController extends Controller
{
    /**
     * @Route("/roles", name="user_roles")
     */
    public function rolesAction()
    {
        /** @var User[] $users */
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('admin/roles.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/promote-editor/{id}", name="promote_editor")
     *
     * @param $id
     */
    public function promoteEditor($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($user === null) {
            $this->addFlash('error', 'Such user does not exist.');
            return $this->redirectToRoute('user_roles');
        }

        $role = $this->getDoctrine()->getRepository(Role::class)->findByName('ROLE_EDITOR');
        $user->addRole($role);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', $user->getFullName() . ' successfully promoted to Editor.');
        return $this->redirectToRoute('user_roles');
    }

    /**
     * @Route("/demote-editor/{id}", name="demote_editor")
     *
     * @param $id
     */
    public function demoteEditor($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($user === null) {
            $this->addFlash('error', 'Such user does not exist.');
            return $this->redirectToRoute('user_roles');
        }

        $role = $this->getDoctrine()->getRepository(Role::class)->findByName('ROLE_EDITOR');
        $user->removeRole($role);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash('warning', $user->getFullName() . ' demoted from Editor.');
        return $this->redirectToRoute('user_roles');
    }

    /**
     * @Route("/promote-admin/{id}", name="promote_admin")
     *
     * @param $id
     */
    public function promoteAdmin($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($user === null) {
            $this->addFlash('error', 'Such user does not exist.');
            return $this->redirectToRoute('user_roles');
        }

        $role = $this->getDoctrine()->getRepository(Role::class)->findByName('ROLE_ADMIN');
        $user->addRole($role);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', $user->getFullName() . ' successfully promoted to Admin.');
        return $this->redirectToRoute('user_roles');
    }

    /**
     * @Route("/demote-admin/{id}", name="demote_admin")
     *
     * @param $id
     */
    public function demoteAdmin($id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($user === null) {
            $this->addFlash('error', 'Such user does not exist.');
            return $this->redirectToRoute('user_roles');
        }

        $role = $this->getDoctrine()->getRepository(Role::class)->findByName('ROLE_ADMIN');
        $user->removeRole($role);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->addFlash('warning', $user->getFullName() . ' demoted from Admin.');
        return $this->redirectToRoute('user_roles');
    }
}
