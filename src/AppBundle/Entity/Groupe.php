<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Groupe
 *
 * @ORM\Table(name="groupe")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GroupeRepository")
 */
class Groupe
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
	 * @Assert\Length(min=10)
     */
    private $name;
	
    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;
	
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
	
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10)
	 * @Assert\Length(min=10)
     */
    private $code;
	
    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     */
	 
	private $founder;
	
	/**
	* @ORM\OneToMany(targetEntity="AppBundle\Entity\UserGroupe", mappedBy="groupe", orphanRemoval=true, cascade={"persist"})
	*/

	private $userGroupes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
	 * @Assert\DateTime()
     */
    private $date;
	
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastUpdate", type="datetime", nullable=true)
	 * @Assert\DateTime()
     */
    private $lastUpdate;

    /**
     * @var int
     *
     * @ORM\Column(name="statut", type="integer")
     */
    private $statut;
	/*
	* 1 : Access by invitation
	* 2 : Access by all (members or not)
	*/
	
    /**
     * @var int
     *
     * @ORM\Column(name="authPost", type="integer")
     */
    private $authPost = 1;
	/*
	* 1 : All members can post
	* 0 : Only administrator can post
	*/
	
    /**
     * @var int
     *
     * @ORM\Column(name="authInvitation", type="integer")
     */
    private $authInvitation = 1;


	public function __construct() {
		$this->date = new \Datetime();
		$this->userGroupes = new ArrayCollection();
		$this->statut = 1;
		//$this->code = $this -> generateCode();
	}
	
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Groupe
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Groupe
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
	

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return Groupe
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }
	
	public function updateLastUpdate()
	{
		$this -> lastUpdate = new \DateTime();
		
		return $this;
	}

    /**
     * Set statut
     *
     * @param integer $statut
     *
     * @return Groupe
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return int
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Groupe
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Add userGroupe
     *
     * @param \AppBundle\Entity\UserGroupe $userGroupe
     *
     * @return Groupe
     */
    public function addUserGroupe(\AppBundle\Entity\UserGroupe $userGroupe)
    {
        $this->userGroupes[] = $userGroupe;

        return $this;
    }

    /**
     * Remove userGroupe
     *
     * @param \AppBundle\Entity\UserGroupe $userGroupe
     */
    public function removeUserGroupe(\AppBundle\Entity\UserGroupe $userGroupe)
    {
        $this->userGroupes->removeElement($userGroupe);
    }

    /**
     * Get usersGroupe
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserGroupes()
    {
        return $this->userGroupes;
    }
	
	public function getNumberUsers() {
		return sizeof($this -> userGroupes);
	}
	
	public function deleteUser(\AppBundle\Entity\User $user) {
		foreach ($this -> userGroupes as $userGroupe) {
			if ($userGroupe -> getUser() == $user) {
				$this -> removeUserGroupe($userGroupe);
				//return 'ok';
			}
		}
		//return 'no';
	}

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Groupe
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
	
	
	/*public function generateCode($lenght = 5) {
		$char = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$char_shuffle = str_shuffle($char);
		$this->code = substr($char_shuffle‚ 1‚ 6);
		//return substr($char_shuffle‚ 0‚ $lenght);
		return 'essai';
	}*/


    /**
     * Set authPost
     *
     * @param integer $authPost
     *
     * @return Groupe
     */
    public function setAuthPost($authPost)
    {
        $this->authPost = $authPost;

        return $this;
    }

    /**
     * Get authPost
     *
     * @return integer
     */
    public function getAuthPost()
    {
        return $this->authPost;
    }

    /**
     * Set authInvitation
     *
     * @param integer $authInvitation
     *
     * @return Groupe
     */
    public function setAuthInvitation($authInvitation)
    {
        $this->authInvitation = $authInvitation;

        return $this;
    }

    /**
     * Get authInvitation
     *
     * @return integer
     */
    public function getAuthInvitation()
    {
        return $this->authInvitation;
    }

    /**
     * Set founder
     *
     * @param \AppBundle\Entity\User $founder
     *
     * @return Groupe
     */
    public function setFounder(\AppBundle\Entity\User $founder)
    {
        $this->founder = $founder;

        return $this;
    }

    /**
     * Get founder
     *
     * @return \AppBundle\Entity\User
     */
    public function getFounder()
    {
        return $this->founder;
    }
	
	public function checkAdministrator(\AppBundle\Entity\User $user) {
		foreach ($this -> userGroupes as $userGroupe) {
			if (($userGroupe -> getUser() == $user) && ($userGroupe -> getIsAdministrator()))
				return true;
		}
		return false;
	}
	
	public function checkMember(\AppBundle\Entity\User $user) {
		foreach ($this -> userGroupes as $userGroupe) {
			if ($userGroupe -> getUser() == $user)
				return true;
		}
		return false;
	}
	
	public function checkNews(\AppBundle\Entity\User $user) {
		foreach ($this -> userGroupes as $userGroupe) {
			if ($userGroupe -> getUser() == $user)
				if ($userGroupe -> getLastVisit() < $this -> lastUpdate)
					return true;
		}
		return false;
	}
	
	public function checkLastVisit(\AppBundle\Entity\User $user) {
		foreach ($this -> userGroupes as $userGroupe) {
			if ($userGroupe -> getUser() == $user)
				return $userGroupe -> getLastVisit();
		}
		return false;
	}
	
	public function updateLastVisit(\AppBundle\Entity\User $user) {
		foreach ($this -> userGroupes as $userGroupe) {
			if ($userGroupe -> getUser() == $user)
				$userGroupe -> setLastVisit(new \Datetime());
		}
		return false;
	}

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Groupe
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
