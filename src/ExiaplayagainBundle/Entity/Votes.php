<?php

namespace ExiaplayagainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Votes
 *
 * @ORM\Table(name="votes", indexes={@ORM\Index(name="game_1", columns={"game_1"}), @ORM\Index(name="game_2", columns={"game_2"}), @ORM\Index(name="game_3", columns={"game_3"}), @ORM\Index(name="game_4", columns={"game_4"})})
 * @ORM\Entity
 */
class Votes
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="limited_date", type="datetime", nullable=false)
     */
    private $limitedDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \ExiaplayagainBundle\Entity\Games
     *
     * @ORM\ManyToOne(targetEntity="ExiaplayagainBundle\Entity\Games")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="game_1", referencedColumnName="id")
     * })
     */
    private $game1;

    /**
     * @var \ExiaplayagainBundle\Entity\Games
     *
     * @ORM\ManyToOne(targetEntity="ExiaplayagainBundle\Entity\Games")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="game_2", referencedColumnName="id")
     * })
     */
    private $game2;

    /**
     * @var \ExiaplayagainBundle\Entity\Games
     *
     * @ORM\ManyToOne(targetEntity="ExiaplayagainBundle\Entity\Games")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="game_3", referencedColumnName="id")
     * })
     */
    private $game3;

    /**
     * @var \ExiaplayagainBundle\Entity\Games
     *
     * @ORM\ManyToOne(targetEntity="ExiaplayagainBundle\Entity\Games")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="game_4", referencedColumnName="id")
     * })
     */
    private $game4;



    /**
     * Set limitedDate
     *
     * @param \DateTime $limitedDate
     * @return Votes
     */
    public function setLimitedDate($limitedDate)
    {
        $this->limitedDate = $limitedDate;

        return $this;
    }

    /**
     * Get limitedDate
     *
     * @return \DateTime 
     */
    public function getLimitedDate()
    {
        return $this->limitedDate;
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
     * Set game1
     *
     * @param \ExiaplayagainBundle\Entity\Games $game1
     * @return Votes
     */
    public function setGame1(\ExiaplayagainBundle\Entity\Games $game1 = null)
    {
        $this->game1 = $game1;

        return $this;
    }

    /**
     * Get game1
     *
     * @return \ExiaplayagainBundle\Entity\Games 
     */
    public function getGame1()
    {
        return $this->game1;
    }

    /**
     * Set game2
     *
     * @param \ExiaplayagainBundle\Entity\Games $game2
     * @return Votes
     */
    public function setGame2(\ExiaplayagainBundle\Entity\Games $game2 = null)
    {
        $this->game2 = $game2;

        return $this;
    }

    /**
     * Get game2
     *
     * @return \ExiaplayagainBundle\Entity\Games 
     */
    public function getGame2()
    {
        return $this->game2;
    }

    /**
     * Set game3
     *
     * @param \ExiaplayagainBundle\Entity\Games $game3
     * @return Votes
     */
    public function setGame3(\ExiaplayagainBundle\Entity\Games $game3 = null)
    {
        $this->game3 = $game3;

        return $this;
    }

    /**
     * Get game3
     *
     * @return \ExiaplayagainBundle\Entity\Games 
     */
    public function getGame3()
    {
        return $this->game3;
    }

    /**
     * Set game4
     *
     * @param \ExiaplayagainBundle\Entity\Games $game4
     * @return Votes
     */
    public function setGame4(\ExiaplayagainBundle\Entity\Games $game4 = null)
    {
        $this->game4 = $game4;

        return $this;
    }

    /**
     * Get game4
     *
     * @return \ExiaplayagainBundle\Entity\Games 
     */
    public function getGame4()
    {
        return $this->game4;
    }

    private $userHasVoted;

    public function setUserHasVoted($userHasVoted)
    {
        $this->userHasVoted = $userHasVoted;

        return $this;
    }

    public function getUserHasVoted()
    {
        return $this->userHasVoted;
    }

    private $votedGame;

    public function setVotedGame($votedGame)
    {
        $this->votedGame = $votedGame;

        return $this;
    }

    public function getVotedGame()
    {
        return $this->votedGame;
    }

    private $numVotesGame1;

    public function getNumVotesGame1()
    {
        return $this->numVotesGame1;
    }

    public function setNumVotesGame1($numVotesGame1)
    {
        $this->numVotesGame1 = $numVotesGame1;

        return $this;
    }

    private $percentVotesGame1;

    public function getPercentVotesGame1()
    {
        return $this->percentVotesGame1;
    }

    public function setPercentVotesGame1($percentVotesGame1)
    {
        $this->percentVotesGame1 = $percentVotesGame1;

        return $this;
    }

    private $numVotesGame2;

    public function getNumVotesGame2()
    {
        return $this->numVotesGame2;
    }

    public function setNumVotesGame2($numVotesGame2)
    {
        $this->numVotesGame2 = $numVotesGame2;

        return $this;
    }

    private $percentVotesGame2;

    public function getPercentVotesGame2()
    {
        return $this->percentVotesGame2;
    }

    public function setPercentVotesGame2($percentVotesGame2)
    {
        $this->percentVotesGame2 = $percentVotesGame2;

        return $this;
    }

    private $numVotesGame3;

    public function getNumVotesGame3()
    {
        return $this->numVotesGame3;
    }

    public function setNumVotesGame3($numVotesGame3)
    {
        $this->numVotesGame3 = $numVotesGame3;

        return $this;
    }

    private $percentVotesGame3;

    public function getPercentVotesGame3()
    {
        return $this->percentVotesGame3;
    }

    public function setPercentVotesGame3($percentVotesGame3)
    {
        $this->percentVotesGame3 = $percentVotesGame3;

        return $this;
    }

    private $numVotesGame4;

    public function getNumVotesGame4()
    {
        return $this->numVotesGame4;
    }

    public function setNumVotesGame4($numVotesGame4)
    {
        $this->numVotesGame4 = $numVotesGame4;

        return $this;
    }

    private $percentVotesGame4;

    public function getPercentVotesGame4()
    {
        return $this->percentVotesGame4;
    }

    public function setPercentVotesGame4($percentVotesGame4)
    {
        $this->percentVotesGame4 = $percentVotesGame4;

        return $this;
    }

    private $totalUsersVotes;

    public function getTotalUsersVotes()
    {
        return $this->totalUsersVotes;
    }

    public function setTotalUsersVotes($totalUsersVotes)
    {
        $this->totalUsersVotes = $totalUsersVotes;

        return $this;
    }
}
