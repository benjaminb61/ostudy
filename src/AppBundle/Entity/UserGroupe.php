<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * UserGroupe
 *
 * @ORM\Table(name="groupe_usergroupe")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserGroupeRepository")
 */
class UserGroupe
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateJoin", type="datetime")
     */
    private $dateJoin;
	
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastVisit", type="datetime", nullable=true)
     */
    private $lastVisit;
	
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Groupe", inversedBy="userGroupes"))
     * @ORM\JoinColumn(nullable=false)
     */
    private $groupe;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
	
    /**
     * @var int
     *
     * @ORM\Column(name="isAdministrator", type="integer", nullable=true)
     */
    private $isAdministrator;
	
	public function __construct() {
		$this->dateJoin = new \Datetime();
		$this->isAdministrator = 0;
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
     * Set dateJoin
     *
     * @param \DateTime $dateJoin
     *
     * @return UserGroupe
     */
    public function setDateJoin($dateJoin)
    {
        $this->dateJoin = $dateJoin;

        return $this;
    }

    /**
     * Get dateJoin
     *
     * @return \DateTime
     */
    public function getDateJoin()
    {
        return $this->dateJoin;
    }
	
    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return UserGroupe
     */
    public function setUser(\AppBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set groupe
     *
     * @param \AppBundle\Entity\Groupe $groupe
     *
     * @return UserGroupe
     */
    public function setGroupe(\AppBundle\Entity\Groupe $groupe)
    {
        $this->groupe = $groupe;

        return $this;
    }

    /**
     * Get groupe
     *
     * @return \AppBundle\Entity\Groupe
     */
    public function getGroupe()
    {
        return $this->groupe;
    }

    /**
     * Set isAdministrator
     *
     * @param integer $isAdministrator
     *
     * @return UserGroupe
     */
    public function setIsAdministrator($isAdministrator)
    {
        $this->isAdministrator = $isAdministrator;

        return $this;
    }

    /**
     * Get isAdministrator
     *
     * @return integer
     */
    public function getIsAdministrator()
    {
        return $this->isAdministrator;
    }

    /**
     * Set lastVisit
     *
     * @param \DateTime $lastVisit
     *
     * @return UserGroupe
     */
    public function setLastVisit($lastVisit)
    {
        $this->lastVisit = $lastVisit;

        return $this;
    }

    /**
     * Get lastVisit
     *
     * @return \DateTime
     */
    public function getLastVisit()
    {
        return $this->lastVisit;
    }
}
