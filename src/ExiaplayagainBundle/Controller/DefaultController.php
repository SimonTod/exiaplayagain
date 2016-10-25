<?php

namespace ExiaplayagainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $session = $request->getSession();

        return $this->render('ExiaplayagainBundle:Default:index.html.twig', array(
            'session' => $session->all(),
        ));
    }

    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        $users = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository('ExiaplayagainBundle:Users');

        if ($session->has('login')) {
            return new RedirectResponse($this->generateUrl('exiaplayagain_homepage'));
        } else {
            if ($request->isMethod('POST')) {
                $check_avail_login = count($user = $users->findByUsername($_POST['login']));

                if ($check_avail_login != 0) {
                    $user = $users->findOneByUsername($_POST['login']);

                    $username = $user->getUsername();

                    if (password_verify($_POST['password'], $user->getPassword())) {
                        $session->set('login', $_POST['login']);
                        if ($user->getIsAdmin())
                        {
                            $session->set('is_admin', true);
                        }
                        $session->getFlashBag()->add('notice', 'Utilisateur connecté');
                    } else {
                        $session->getFlashBag()->add('notice', 'Utilisateur ou mot de passe incorrect');
                        return $this->render('ExiaplayagainBundle:Default:login.html.twig');
                    }

                    //return new RedirectResponse($this->generateUrl('portfolio_homepage'));
                    return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
                }
                else
                {
                    $session->getFlashBag()->add('notice', 'Utilisateur ou mot de passe incorrect');
                    return $this->render('ExiaplayagainBundle:Default:login.html.twig');
                }

            } else {

                return $this->render('ExiaplayagainBundle:Default:login.html.twig');

            }
        }

    }

    public function logoutAction(Request $request)
    {
        $session = $request->getSession();

        if ($session->has('login')) {
            $session->clear();
        }

        return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function myaccountAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this
            ->getDoctrine()
            ->getManager();
        $user = $em
            ->getRepository('ExiaplayagainBundle:Users')
            ->findOneByUsername($session->get('login'));

        if ($session->has('login')) {
            if ($request->isMethod('POST')) {
                if (password_verify($_POST['old_password'], $user->getPassword()))
                {
                    if ($_POST['new_password_1'] == $_POST['new_password_2'])
                    {
                        $user->setPassword(password_hash($_POST['new_password_1'], PASSWORD_DEFAULT));

                        $em->persist($user);

                        $em->flush();

                        $session->getFlashBag()->add('notice', 'Mot de passe modifié avec succès');
                        return $this->redirectToRoute('exiaplayagain_homepage');
                    }
                    else
                    {
                        $session->getFlashBag()->add('notice', 'Veuillez entrer 2 fois le même nouveau mot de passe');
                        return $this->render('ExiaplayagainBundle:Default:myaccount.html.twig');
                    }
                }
                else
                {
                    $session->getFlashBag()->add('notice', "Votre ancien mot de passe n'est pas correct");
                    return $this->render('ExiaplayagainBundle:Default:myaccount.html.twig');
                }

            }
            else {
                return $this->render('ExiaplayagainBundle:Default:myaccount.html.twig');
            }
        }
        else {
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
        }
    }
}

