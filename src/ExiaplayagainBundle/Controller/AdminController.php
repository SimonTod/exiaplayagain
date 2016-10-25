<?php

namespace ExiaplayagainBundle\Controller;

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
}
