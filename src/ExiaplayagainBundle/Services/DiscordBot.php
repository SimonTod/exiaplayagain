<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 16/03/17
 * Time: 20:31
 */

// src/ExiaplayagainBundle/Services/DiscordBot.php
namespace ExiaplayagainBundle\Services;



class DiscordBot
{
    private $DISCORD_API = "https://discordapp.com/api";
    private $DISCORD_BOTTOKEN = 'MjkwNzcyNTY2MzUwNjI2ODM2.C6hHFQ.Fi6eGTUpQt8lZ78Z8qTEShKAXwk'; // bot-exiaplayagain
//    private $DISCORD_GUILDID = '220860793451708417'; // eXia PAU
    private $DISCORD_GUILDID = '260074341033705474'; // Forains
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
}
