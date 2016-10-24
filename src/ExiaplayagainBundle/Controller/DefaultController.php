<?php

namespace ExiaplayagainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
                    //$salt = exec("cat /safety/salt.txt");

                    //if ($user->getPassword() == md5($_POST['password'].$salt.$username.$salt.$username)) {
                    if ($user->getPassword() == $_POST['password']) {
                        $session->set('login', $_POST['login']);
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
}

