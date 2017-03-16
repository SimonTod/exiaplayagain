<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 16/03/17
 * Time: 20:31
 */

// src/ExiaplayagainBundle/Services/DiscordBot.php
namespace ExiaplayagainBundle\Services;

use ExiaplayagainBundle\Entity\DiscordTokens;

class DiscordBot
{
    private $DISCORD_API = "https://discordapp.com/api";
    private $DISCORD_BOTTOKEN = 'MjkwNzcyNTY2MzUwNjI2ODM2.C6hHFQ.Fi6eGTUpQt8lZ78Z8qTEShKAXwk'; // bot-exiaplayagain
    private $DISCORD_GUILDID = '220860793451708417'; // eXia PAU
//    private $DISCORD_GUILDID = '260074341033705474'; // Forains
    private $DISCORD_ASSOCHANNELID = "245481987974889472";

    protected $em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    public function postMessage($content, $channelId) {
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

    public function createPrivateConvo($userDiscordId) {
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

    public function sendPrivateMessage($userDiscordId, $content) {
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

    public function createVerificationToken($user, $ip) {
        $em = $this->em;
        $discord_token = new DiscordTokens();
        $discord_token->setType(0);
        $discord_token->setUser($user);
        $discord_token->setIp($ip);
        $em->persist($discord_token);
        $em->flush();

        $message = $this->sendPrivateMessage($user->getDiscordId(), "https://exiaplayagain.tk/discordbot/verify/".$discord_token->getToken());
        if ($message['success'] == true)
            $message['message'] = "Un lien de vérification a été envoyé à votre compte Discord. Il est valable 5 minutes. Veuillez l'ouvrir pour finaliser la procédure";
        return $message;
    }

    public function createLoginToken($user, $ip) {
        $em = $this->em;
        $discord_token = new DiscordTokens();
        $discord_token->setType(1);
        $discord_token->setUser($user);
        $discord_token->setIp($ip);
        $em->persist($discord_token);
        $em->flush();

        $message = $this->sendPrivateMessage($user->getDiscordId(), "https://exiaplayagain.tk/discordbot/login/".$discord_token->getToken());
        if ($message['success'] == true)
            $message['message'] = "Un lien de connexion a été envoyé à votre compte Discord. Il est valable 5 minutes. Veuillez l'ouvrir pour vous connecter";
        return $message;
    }

    public function getUsersList($guildId) {
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

    public function getUserId($usersList, $username, $discriminator) {
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

    public function deleteUserTokens($user) {
        $em = $this->em;
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
