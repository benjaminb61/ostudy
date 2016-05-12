<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
	
    /**
     * @var int
     *
     * @ORM\Column(name="sum_vote", type="integer", nullable=true)
     */
    private $sumVote;
	
    /**
     * @var int
     *
     * @ORM\Column(name="sum_members", type="integer", nullable=true)
     */
    private $sumMembers;
	
    /**
     * @var int
     *
     * @ORM\Column(name="is_seek_job", type="integer", nullable=true)
     */
    private $isSeekJob;
	
    /**
     * @var int
     *
     * @ORM\Column(name="is_premium", type="integer", nullable=true)
     */
    private $isPremium;
	
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_end_premium", type="datetime", nullable=true)
	 * @Assert\DateTime()
     */
    private $dateEndPremium;

    public function __construct()
    {
        parent::__construct();
		$this->sumVote = 0;
		$this->sumMembers = 0;
		$this->isSeekJob = 0;
		$this->isPremium = 0;
        // your own logic
    }

    /**
     * Set sumVote
     *
     * @param integer $vote
     *
     * @return User
     */
    public function setSumVote($sumVote)
    {
        $this->sumVote = $sumVote;

        return $this;
    }

    /**
     * Get sumVote
     *
     * @return integer
     */
    public function getSumVote()
    {
        return $this->sumVote;
    }
	
	public function increaseSumVote()
	{
		$this->setSumVote($nbVote = $this->sumVote + 1);
		return $nbVote;
	}
	
	public function decreaseSumVote()
	{
		$this->setSumVote($nbVote = $this->sumVote - 1);
		return $nbVote;
	}

    /**
     * Set isSeekJob
     *
     * @param integer $seekJob
     *
     * @return User
     */
    public function setIsSeekJob($isSeekJob)
    {
        $this->isSeekJob = $isSeekJob;

        return $this;
    }

    /**
     * Get isSeekJob
     *
     * @return integer
     */
    public function getIsSeekJob()
    {
        return $this->isSeekJob;
    }

    /**
     * Set sumMembers
     *
     * @param integer $sumMembers
     *
     * @return User
     */
    public function setSumMembers($sumMembers)
    {
        $this->sumMembers = $sumMembers;

        return $this;
    }

    /**
     * Get sumMembers
     *
     * @return integer
     */
    public function getSumMembers()
    {
        return $this->sumMembers;
    }
	
	public function increaseSumMembers()
	{
		$this->setSumMembers($nbMembers = $this->sumMembers + 1);
		return $nbMembers;
	}
	
	public function decreaseSumMembers()
	{
		$this->setSumMembers($nbMembers = $this->sumMembers - 1);
		return $nbMembers;
	}

    /**
     * Set isPremium
     *
     * @param integer $isPremium
     *
     * @return User
     */
    public function setIsPremium($isPremium)
    {
        $this->isPremium = $isPremium;

        return $this;
    }

    /**
     * Get isPremium
     *
     * @return integer
     */
    public function getIsPremium()
    {
        return $this->isPremium;
    }

    /**
     * Set dateEndPremium
     *
     * @param \DateTime $dateEndPremium
     *
     * @return User
     */
    public function setDateEndPremium($dateEndPremium)
    {
        $this->dateEndPremium = $dateEndPremium;

        return $this;
    }

    /**
     * Get dateEndPremium
     *
     * @return \DateTime
     */
    public function getDateEndPremium()
    {
        return $this->dateEndPremium;
    }
}
