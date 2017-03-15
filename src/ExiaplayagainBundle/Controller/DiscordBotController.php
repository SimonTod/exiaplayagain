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
    private $DISCORD_API = "https://discordapp.com/api";
    private $DISCORD_BOTTOKEN = 'MjkwNzcyNTY2MzUwNjI2ODM2.C6hHFQ.Fi6eGTUpQt8lZ78Z8qTEShKAXwk';
    private $DISCORD_GUILDID = '220860793451708417';
    private $DISCORD_ASSOCHANNELID = "245481987974889472";

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

        if ($this->checkAdmin($session)) {
            if ($request->isMethod('POST')) {
                $data = $this->postMessage($_POST['content'], $_POST['channel_id']);
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

        if ($session->has('login')) {
            if ($request->isMethod('POST')) {
                $usersList = $this->getUsersList($this->DISCORD_GUILDID);
                $username = explode("#", $_POST['username'])[0];
                $userDiscriminator = explode("#", $_POST['username'])[1];
                $userId = $this->getUserId($usersList, $username, $userDiscriminator);
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
                    $response = $this->createVerificationToken($user);

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
                if ($bdd_token->getValidity() > new \DateTime()) {
                    $user->setDiscordIsVerified(true);
                    $em->persist($user);
                    $em->remove($bdd_token);
                    $em->flush();
                    $session->getFlashBag()->add('notice', "Votre compte Discord est associé à votre compte ExiaPlayAgain. Vous pouvez maintenant vous connecter en utiliser des liens générés. D'autres fonctionnalités sont en développement.");
                } else
                    $session->getFlashBag()->add('error', "Le lien est expiré, veuillez renseigner à nouveau votre username Discord pour recevoir un nouveau lien");
            } else
                $session->getFlashBag()->add('error', "Le lien n'est pas valide, veuillez renseigner à nouveau votre username Discord pour recevoir un nouveau lien");
            return $this->redirect($this->generateUrl('exiaplayagain_myaccount'));
        } else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function sendconnectionlinkAction(Request $request) {
        $session = $request->getSession();
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
                        $response = $this->createLoginToken($user);

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
                if ($bdd_token->getValidity() > new \DateTime()) {
                    $user = $bdd_token->getUser();
                    $session->set('login', $user->getUsername());
                    if ($user->getIsAdmin())
                    {
                        $session->set('is_admin', true);
                    }
                    $this->deleteUserTokens($user);
                    $session->getFlashBag()->add('notice', "Utilisateur connecté");
                    return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
                } else
                    $session->getFlashBag()->add('error', "Le lien est expiré, veuillez renseigner à nouveau votre username pour recevoir un nouveau lien");
            } else
                $session->getFlashBag()->add('error', "Le lien n'est pas valide, veuillez renseigner à nouveau votre username pour recevoir un nouveau lien");
            return $this->redirect($this->generateUrl('exiaplayagain_login'));
        } else
            return $this->redirect($this->generateUrl('exiaplayagain_homepage'));
    }

    public function sendusernameAction(Request $request) {
        $session = $request->getSession();
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
                    $this->sendPrivateMessage($users[0]->getDiscordId(), $message);
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

    private function postMessage($content, $channelId) {
        $fields = array(
            'content' => $content
        );
        $json_fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->DISCORD_API. "/channels/". $channelId ."/messages");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: Bot '.$this->DISCORD_BOTTOKEN,
            'Content-Length: ' . strlen($json_fields)));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_fields);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        return json_decode($response, true);
    }

    private function createPrivateConvo($userDiscordId) {
        $fields = array(
            'recipient_id' => $userDiscordId
        );
        $json_fields = json_encode($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->DISCORD_API. "/users/@me/channels");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: Bot '.$this->DISCORD_BOTTOKEN,
            'Content-Length: ' . strlen($json_fields)));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_fields);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $data = json_decode($response, true);
        return $data;
    }

    private function getUsersList($guildId) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->DISCORD_API. "/guilds/" . $guildId . "/members?limit=1000");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/json',
            'Authorization: Bot '.$this->DISCORD_BOTTOKEN));
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        return json_decode($response, true);
    }

    private function getUserId($usersList, $username, $discriminator) {
        for ($i = 0; $i < count($usersList); $i++) {
            if ($usersList[$i]['user']['username'] == $username && $usersList[$i]['user']['discriminator'] == $discriminator) {
                $userId = $usersList[$i]['user']['id'];
            }
        }
        if (isset($userId))
            return $userId;
        else
            return null;
    }

    private function sendPrivateMessage($userDiscordId, $content) {
        $convo = $this->createPrivateConvo($userDiscordId);
        if ((array_key_exists("code", $convo) && array_key_exists('message', $convo)) || (!array_key_exists("id", $convo))) {
            return array(
                "success" => false,
                "message" => "Erreur lors de la création d'une conversation privée avec l'utilisateur"
            );
        } else {
            $channel_id = $convo['id'];
            $message = $this->postMessage($content, $channel_id);

            if (array_key_exists("code", $message) && array_key_exists('message', $message)) {
                return array(
                    "success" => false,
                    "message" => "Erreur lors de l'envoi du message à l'utilisateur"
                );
            } else {
                return array(
                    "success" => true
                );
            }
        }
    }

    private function createVerificationToken($user) {
        $em = $this
            ->getDoctrine()
            ->getManager();
        $discord_token = new DiscordTokens();
        $discord_token->setType(0);
        $discord_token->setUser($user);
        $em->persist($discord_token);
        $em->flush();

        $message = $this->sendPrivateMessage($user->getDiscordId(), "https://exiaplayagain.tk/discordbot/verify/".$discord_token->getToken());
        if ($message['success'] == true)
            $message['message'] = "Un lien de vérification a été envoyé à votre compte Discord. Il est valable 5 minutes. Veuillez l'ouvrir pour finaliser la procédure";
        return $message;
    }

    private function createLoginToken($user) {
        $em = $this
            ->getDoctrine()
            ->getManager();
        $discord_token = new DiscordTokens();
        $discord_token->setType(1);
        $discord_token->setUser($user);
        $em->persist($discord_token);
        $em->flush();

        $message = $this->sendPrivateMessage($user->getDiscordId(), "https://exiaplayagain.tk/discordbot/login/".$discord_token->getToken());
        if ($message['success'] == true)
            $message['message'] = "Un lien de connexion a été envoyé à votre compte Discord. Il est valable 5 minutes. Veuillez l'ouvrir pour vous connecter";
        return $message;
    }

    private function deleteUserTokens($user) {
        $em = $this
            ->getDoctrine()
            ->getManager();
        $tokens = $em
            ->getRepository('ExiaplayagainBundle:DiscordTokens')
            ->findBy(array("user" => $user),    //where
                array('validity' => 'DESC')//order
            );
        foreach ($tokens as $token) {
            $em->remove($token);
        }
        $em->flush();
    }

}
