<?php

namespace ExiaplayagainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Users
 *
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"username"})})
 * @ORM\Entity
 */
class Users
{
    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_admin", type="boolean", nullable=false)
     */
    private $isAdmin;

    /**
     * @var string
     *
     * @ORM\Column(name="discord_username", type="string", length=255, nullable=true)
     */
    private $discordUsername;

    /**
     * @var integer
     *
     * @ORM\Column(name="discord_id", type="bigint", nullable=true)
     */
    private $discordId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="discord_is_verified", type="boolean", nullable=false)
     */
    private $discordIsVerified;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;



    /**
     * Set username
     *
     * @param string $username
     * @return Users
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Users
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Users
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set isAdmin
     *
     * @param boolean $isAdmin
     * @return Users
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Get isAdmin
     *
     * @return boolean 
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Set discordUsername
     *
     * @param string $discordUsername
     * @return Users
     */
    public function setDiscordUsername($discordUsername)
    {
        $this->discordUsername = $discordUsername;

        return $this;
    }

    /**
     * Get discordUsername
     *
     * @return string 
     */
    public function getDiscordUsername()
    {
        return $this->discordUsername;
    }

    /**
     * Set discordId
     *
     * @param integer $discordId
     * @return Users
     */
    public function setDiscordId($discordId)
    {
        $this->discordId = $discordId;

        return $this;
    }

    /**
     * Get discordId
     *
     * @return integer 
     */
    public function getDiscordId()
    {
        return $this->discordId;
    }

    /**
     * Set discordIsVerified
     *
     * @param boolean $discordIsVerified
     * @return Users
     */
    public function setDiscordIsVerified($discordIsVerified)
    {
        $this->discordIsVerified = $discordIsVerified;

        return $this;
    }

    /**
     * Get discordIsVerified
     *
     * @return boolean 
     */
    public function getDiscordIsVerified()
    {
        return $this->discordIsVerified;
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
}
