<?php

namespace ExiaplayagainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsersVotes
 *
 * @ORM\Table(name="users_votes", indexes={@ORM\Index(name="user", columns={"user"}), @ORM\Index(name="vote", columns={"vote"}), @ORM\Index(name="game", columns={"game"})})
 * @ORM\Entity
 */
class UsersVotes
{
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
     * @var \ExiaplayagainBundle\Entity\Votes
     *
     * @ORM\ManyToOne(targetEntity="ExiaplayagainBundle\Entity\Votes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vote", referencedColumnName="id")
     * })
     */
    private $vote;

    /**
     * @var \ExiaplayagainBundle\Entity\Games
     *
     * @ORM\ManyToOne(targetEntity="ExiaplayagainBundle\Entity\Games")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="game", referencedColumnName="id")
     * })
     */
    private $game;



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
     * @return UsersVotes
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

    /**
     * Set vote
     *
     * @param \ExiaplayagainBundle\Entity\Votes $vote
     * @return UsersVotes
     */
    public function setVote(\ExiaplayagainBundle\Entity\Votes $vote = null)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return \ExiaplayagainBundle\Entity\Votes 
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * Set game
     *
     * @param \ExiaplayagainBundle\Entity\Games $game
     * @return UsersVotes
     */
    public function setGame(\ExiaplayagainBundle\Entity\Games $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \ExiaplayagainBundle\Entity\Games 
     */
    public function getGame()
    {
        return $this->game;
    }
}
