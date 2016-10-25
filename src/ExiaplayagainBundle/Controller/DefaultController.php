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


}

