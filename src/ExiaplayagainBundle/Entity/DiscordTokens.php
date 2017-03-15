<?php

namespace ExiaplayagainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DiscordTokens
 *
 * @ORM\Table(name="discord_tokens", indexes={@ORM\Index(name="user", columns={"user"})})
 * @ORM\Entity
 */
class DiscordTokens
{
    public function __construct()
    {
        $this->validity = null;
        $this->token = rand(0, 2000000000);
    }

    /**
     * @var integer
     *
     * @ORM\Column(name="token", type="integer", nullable=false)
     */
    private $token;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=false)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="validity", type="datetime", nullable=false)
     */
    private $validity;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255, nullable=false)
     */
    private $ip;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ExiaplayagainBundle\Entity\Users
     *
     * @ORM\ManyToOne(targetEntity="ExiaplayagainBundle\Entity\Users")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user", referencedColumnName="id")
     * })
     */
    private $user;



    /**
     * Set token
     *
     * @param integer $token
     * @return DiscordTokens
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return integer 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return DiscordTokens
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set validity
     *
     * @param \DateTime $validity
     * @return DiscordTokens
     */
    public function setValidity($validity)
    {
        $this->validity = $validity;

        return $this;
    }

    /**
     * Get validity
     *
     * @return \DateTime 
     */
    public function getValidity()
    {
        return $this->validity;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return DiscordTokens
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \ExiaplayagainBundle\Entity\Users $user
     * @return DiscordTokens
     */
    public function setUser(\ExiaplayagainBundle\Entity\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \ExiaplayagainBundle\Entity\Users 
     */
    public function getUser()
    {
        return $this->user;
    }
}
