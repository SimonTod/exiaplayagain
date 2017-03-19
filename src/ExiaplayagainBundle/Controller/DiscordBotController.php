<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 13/03/17
 * Time: 16:20
 */

namespace ExiaplayagainBundle\Controller;

use ExiaplayagainBundle\Entity\DiscordTokens;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DiscordBotController extends Controller
{
    public function homeAction(Request $request) {
        $session = $request->getSession();

        if ($this->checkAdmin($session)) {
            return $this->render('ExiaplayagainBundle:DiscordBot:homepage.html.twig', array(
                'session' => $session->all(),
            ));
        } else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function sendmessageAction(Request $request) {
        $session = $request->getSession();
        $discordBot = $this->get("exiaplayagain.discordbot");

        if ($this->checkAdmin($session)) {
            if ($request->isMethod('POST')) {
                $data = $discordBot->postMessage($_POST['content'], $_POST['channel_id']);
                if (array_key_exists("code", $data) && array_key_exists('message', $data))
                    $session->getFlashBag()->add('discordbot_error', $data);
                else
                    $session->getFlashBag()->add('discordbot_notice', $data);
            }
            return $this->redirect($this->generateUrl('exiaplayagain_discordbot_home'));
        } else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function associateAction(Request $request) {
        $session = $request->getSession();
        $discordBot = $this->get("exiaplayagain.discordbot");

        if ($session->has('login')) {
            if ($request->isMethod('POST')) {
                $usersList = $discordBot->getUsersList();
                $username = explode("#", $_POST['username'])[0];
                $userDiscriminator = explode("#", $_POST['username'])[1];
                $userId = $discordBot->getUserId($usersList, $username, $userDiscriminator);
//                $session->getFlashBag()->add('discordbot_notice', $userId);
                if ($userId != null) {
                    $em = $this
                        ->getDoctrine()
                        ->getManager();
                    $user = $em
                        ->getRepository('ExiaplayagainBundle:Users')
                        ->findOneByUsername($session->get('login'));

                    $user->setDiscordUsername($_POST['username']);
                    $user->setDiscordId($userId);
                    $user->setDiscordIsVerified(false);
                    $em->persist($user);
                    $em->flush();
                    $response = $discordBot->createVerificationToken($user, $request->getClientIp());

                    if ($response['success'] == true)
                        $session->getFlashBag()->add('notice', $response['message']);
                    else
                        $session->getFlashBag()->add('error', $response['message']);
                } else
                    $session->getFlashBag()->add('error', "L'utilisateur " . $_POST['username'] . " n'a pas été trouvé sur le serveur Discord");
            }
            return $this->redirect($this->generateUrl('exiaplayagain_myaccount'));
        } else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function verifyAction(Request $request, $token) {
        $session = $request->getSession();
        if ($session->has('login')) {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $user = $em
                ->getRepository('ExiaplayagainBundle:Users')
                ->findOneByUsername($session->get('login'));
            $bdd_token = $em
                ->getRepository('ExiaplayagainBundle:DiscordTokens')
                ->findOneBy(array(
                    "user" => $user,
                    "type" => 0,
                    "token" => $token),    //where
                    array('validity' => 'DESC')//order
                );
            if ($bdd_token != null) {
                if ($bdd_token->getIp() == $request->getClientIp()) {
                    if ($bdd_token->getValidity() > new \DateTime()) {
                        $user->setDiscordIsVerified(true);
                        $em->persist($user);
                        $em->remove($bdd_token);
                        $em->flush();
                        $session->getFlashBag()->add('notice', "Votre compte Discord est associé à votre compte ExiaPlayAgain. Vous pouvez maintenant vous connecter en utiliser des liens générés. D'autres fonctionnalités sont en développement.");
                    } else
                        $session->getFlashBag()->add('error', "Le lien est expiré, veuillez renseigner à nouveau votre username Discord pour recevoir un nouveau lien");
                } else
                    $session->getFlashBag()->add('error', "Vous tentez de finaliser cette action depuis un poste différent de celui depuis lequel a été fait la demande");
            } else
                $session->getFlashBag()->add('error', "Le lien n'est pas valide, veuillez renseigner à nouveau votre username Discord pour recevoir un nouveau lien");
            return $this->redirect($this->generateUrl('exiaplayagain_myaccount'));
        } else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function sendconnectionlinkAction(Request $request) {
        $session = $request->getSession();
        $discordBot = $this->get("exiaplayagain.discordbot");
        if (!$session->has('login')) {
            if ($request->isMethod('POST')) {
                $em = $this
                    ->getDoctrine()
                    ->getManager();
                $user = $em
                    ->getRepository('ExiaplayagainBundle:Users')
                    ->findOneByUsername($_POST['login']);
                if ($user != null) {
                    if ($user->getDiscordIsVerified() == true) {
                        $response = $discordBot->createLoginToken($user, $request->getClientIp());

                        if ($response['success'] == true)
                            $session->getFlashBag()->add('notice', $response['message']);
                        else
                            $session->getFlashBag()->add('error', $response['message']);
                    } else
                        $session->getFlashBag()->add('error', "Cet utilisateur n'a pas encore ajouté et validé son compte Discord");
                } else
                    $session->getFlashBag()->add('error', "Cet utilisateur n'existe pas");
            }
            return $this->redirect($this->generateUrl('exiaplayagain_login'));
        } else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function loginAction(Request $request, $token) {
        $session = $request->getSession();
        $discordBot = $this->get("exiaplayagain.discordbot");
        if (!$session->has('login')) {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $bdd_token = $em
                ->getRepository('ExiaplayagainBundle:DiscordTokens')
                ->findOneBy(array(
                    "type" => 1,
                    "token" => $token),    //where
                    array('validity' => 'DESC')//order
                );
            if ($bdd_token != null) {
                if ($bdd_token->getIp() == $request->getClientIp())
                {
                    if ($bdd_token->getValidity() > new \DateTime()) {
                        $user = $bdd_token->getUser();
                        $session->set('login', $user->getUsername());
                        if ($user->getIsAdmin())
                        {
                            $session->set('is_admin', true);
                        }
                        $discordBot->deleteUserTokens($user);
                        $session->getFlashBag()->add('notice', "Utilisateur connecté");
                        return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
                    } else
                        $session->getFlashBag()->add('error', "Le lien est expiré, veuillez renseigner à nouveau votre username pour recevoir un nouveau lien");
                } else
                    $session->getFlashBag()->add('error', "Vous tentez de vous connecter sur un poste différent de celui depuis lequel a été fait la demande");
            } else
                $session->getFlashBag()->add('error', "Le lien n'est pas valide, veuillez renseigner à nouveau votre username pour recevoir un nouveau lien");
            return $this->redirect($this->generateUrl('exiaplayagain_login'));
        } else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function sendusernameAction(Request $request) {
        $session = $request->getSession();
        $discordBot = $this->get("exiaplayagain.discordbot");
        if (!$session->has('login')) {
            if ($request->isMethod('POST')) {
                $em = $this
                    ->getDoctrine()
                    ->getManager();
                $users = $em
                    ->getRepository('ExiaplayagainBundle:Users')
                    ->findBy(array("discordUsername" => $_POST['discord-username']));
                if (!count($users) == 0) {
                    if (count($users) == 1) {
                        $message = "Ton username ExiaPlayAgain est **" . $users[0]->getUsername() . "**";
                    } else {
                        $message = "Plusieurs username ExiaPlayAgain sont associés à ce username Discord : **" . $users[0]->getUsername() . "**";
                        for ($i = 1; $i < count($users) - 1; $i++) {
                            $message = $message. ", **" . $users[$i]->getUsername() . "**";
                        }
                        $message = $message. " et **" . $users[count($users)-1]->getUsername() . "**";
                    }
                    $discordBot->sendPrivateMessage($users[0]->getDiscordId(), $message);
                    $session->getFlashBag()->add('notice', "Un message vous a été envoyé sur Discord");
                } else
                    $session->getFlashBag()->add('error', "Aucun utilisateur n'est associé à ce compte Discord");
            }
            return $this->redirect($this->generateUrl('exiaplayagain_login'));
        } else
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
