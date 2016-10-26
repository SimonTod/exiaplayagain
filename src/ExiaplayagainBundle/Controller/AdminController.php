<?php

namespace ExiaplayagainBundle\Controller;

use ExiaplayagainBundle\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function userslistAction(Request $request)
    {
        $session = $request->getSession();

        if ($this->checkAdmin($session)) {
            $users = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('ExiaplayagainBundle:Users')
                ->findBy(
                    array(),    //where
                    array('id' => 'ASC')//order
                );

            return $this->render('ExiaplayagainBundle:Admin:userslist.html.twig', array(
                'session' => $session->all(),
                'users' => $users,
            ));
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function adduserAction(request $request)
    {
        $session = $request->getSession();

        if ($this->checkAdmin($session))
        {
            if($request->isMethod('POST') && $_POST['password1'] == $_POST['password2'] && $this->checkAvailableUsername($_POST['username']))
            {
                $em = $this
                    ->getDoctrine()
                    ->getManager();

                $user = new Users();

                $user->setUsername($_POST['username']);
                $user->setName($_POST['name']);
                $user->setPassword(password_hash($_POST['password1'], PASSWORD_DEFAULT));
                $user->setIsAdmin(false);

                $em->persist($user);

                $em->flush();

                $session->getFlashBag()->add('notice', "Utilisateur ".$_POST['username']." créé avec succès");
                return $this->redirect($this->generateUrl('exiaplayagain_userslist'));
            }
            elseif ($request->isMethod('POST') && !$this->checkAvailableUsername($_POST['username']))
            {
                $session->getFlashBag()->add('error', "Le nom d'utilisateur ".$_POST['username']." est déjà présent dans la liste des utilisateurs");
                return $this->render('ExiaplayagainBundle:Admin:adduser.html.twig', array(
                    'session' => $session->all(),
                ));
            }
            elseif ($request->isMethod('POST') && $_POST['password1'] != $_POST['password2'])
            {
                $session->getFlashBag()->add('error', 'Veuillez entrer 2 fois le même nouveau mot de passe');
                return $this->render('ExiaplayagainBundle:Admin:adduser.html.twig', array(
                    'session' => $session->all(),
                ));
            }
            else
                return $this->render('ExiaplayagainBundle:Admin:adduser.html.twig', array(
                    'session' => $session->all(),
                ));
        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function removeuserAction(Request $request, $username)
    {
        $session = $request->getSession();

        if ($this->checkAdmin($session))
        {
            if(isset($username) && $username != $session->get('login'))
            {
                $em = $this
                    ->getDoctrine()
                    ->getManager();

                $user = $em
                    ->getRepository('ExiaplayagainBundle:Users')
                    ->findOneByUsername($username);

                $em->remove($user);
                $em->flush();

                $session->getFlashBag()->add('notice', "Utilisateur ".$username." supprimé avec succès");
                return $this->redirect($this->generateUrl('exiaplayagain_userslist'));
            }
            else if ($username == $session->get('login'))
            {
                $session->getFlashBag()->add('error', "Vous ne pouvez pas supprimé l'utilisateur avec lequel vous êtes connectés");
                return $this->redirect($this->generateUrl('exiaplayagain_userslist'));
            }

        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function changeisadminAction(Request $request, $username)
    {
        $session = $request->getSession();

        if ($this->checkAdmin($session))
        {
            if(isset($username) && $username != $session->get('login'))
            {
                $em = $this
                    ->getDoctrine()
                    ->getManager();

                $user = $em
                    ->getRepository('ExiaplayagainBundle:Users')
                    ->findOneByUsername($username);

//                $em->remove($user);
//                $em->flush();
//
//                $session->getFlashBag()->add('notice', "Utilisateur ".$username." supprimé avec succès");

                if ($user->getIsAdmin() == true)
                {
                    $user->setIsAdmin(false);

                    $session->getFlashBag()->add('notice', "L'utilisateur ".$username." n'est plus un administrateur");
                }
                else
                {
                    $user->setIsAdmin(true);

                    $session->getFlashBag()->add('notice', "L'utilisateur ".$username." est maintenant un administrateur");
                }

                $em->persist($user);
                $em->flush();

                return $this->redirect($this->generateUrl('exiaplayagain_userslist'));
            }
            else if ($username == $session->get('login'))
            {
                $session->getFlashBag()->add('error', "Vous ne pouvez pas enlver les droits d'administrations pour l'utilisateur avec lequel vous êtes connectés");
                return $this->redirect($this->generateUrl('exiaplayagain_userslist'));
            }

        }
        else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    private function checkAdmin($session)
    {
        if(!$session->has('login'))
            return false;

        $user = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('ExiaplayagainBundle:Users')
            ->findOneByUsername($session->get('login'));

        if($user->getIsAdmin())
            return true;
        else
            return false;
    }

    private function checkAvailableUsername($username)
    {
        $user = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('ExiaplayagainBundle:Users')
            ->findOneByUsername($username);

        if (empty($user))
            return true;
        else
            return false;
    }
}
