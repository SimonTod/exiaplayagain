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
}
