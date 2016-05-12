<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 */
class Event
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
     * @ORM\Column(name="type", type="string", length=50)
     */
    private $type;


    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="statut", type="integer")
     */
    private $statut;
	
	/**
	* @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post", cascade={"persist"})
	* @ORM\JoinColumn(nullable=true)
	*/

	private $post;
	
	/**
	* @ORM\ManyToOne(targetEntity="AppBundle\Entity\Comment", cascade={"persist"})
	* @ORM\JoinColumn(nullable=true)
	*/

	private $comment;
	
	/**
	* @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", cascade={"persist"})
	* @ORM\JoinColumn(nullable=true)
	*/

	private $author;
	
	/**
	* @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", cascade={"persist"})
	* @ORM\JoinColumn(nullable=false)
	*/

	private $users;
	
	/**
	* @ORM\ManyToMany(targetEntity="AppBundle\Entity\Groupe", cascade={"persist"})
	* @ORM\JoinColumn(nullable=true)
	*/

	private $groupes;
	
	public function __construct() {
		$this->date = new \Datetime();
		$this->users = new ArrayCollection();
		$this->groupes = new ArrayCollection();
		$this->statut = 1;
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
     * Set content
     *
     * @param string $content
     *
     * @return Event
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Event
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
     * Set statut
     *
     * @param integer $statut
     *
     * @return Event
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
     * Add user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Event
     */
    public function addUser(\AppBundle\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \AppBundle\Entity\User $user
     */
    public function removeUser(\AppBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Add groupe
     *
     * @param \AppBundle\Entity\Groupe $groupe
     *
     * @return Event
     */
    public function addGroupe(\AppBundle\Entity\Groupe $groupe)
    {
        $this->groupes[] = $groupe;

        return $this;
    }

    /**
     * Remove groupe
     *
     * @param \AppBundle\Entity\Groupe $groupe
     */
    public function removeGroupe(\AppBundle\Entity\Groupe $groupe)
    {
        $this->groupes->removeElement($groupe);
    }

    /**
     * Get groupes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupes()
    {
        return $this->groupes;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Event
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set post
     *
     * @param \AppBundle\Entity\Post $post
     *
     * @return Event
     */
    public function setPost(\AppBundle\Entity\Post $post = null)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \AppBundle\Entity\Post
     */
    public function getPost()
    {
        return $this->post;
    }
	
    /**
     * Set comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return Event
     */
    public function setComment(\AppBundle\Entity\Comment $comment = null)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return \AppBundle\Entity\Comment
     */
    public function getComment()
    {
        return $this->comment;
    }
	
    /**
     * Set author
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Event
     */
    public function setAuthor(\AppBundle\Entity\User $user = null)
    {
        $this->author = $user;

        return $this;
    }

    /**
     * Get author
     *
     * @return \AppBundle\Entity\User
     */
    public function getAuthor()
    {
        return $this->author;
    }
	
	public function getImageEvent() {
		if (!$this -> type)
			return null;
		
		$glyphiconClass = null;
		
		switch ($this -> type) {
			case 'post-add':
				$glyphiconClass = "glyphicon-align-left";
				break;
			case 'post-edit':
				$glyphiconClass = "glyphicon-edit";
				break;
			case 'comment-add':
				$glyphiconClass = "glyphicon-comment";
				break;
		}
		if ($glyphiconClass)
			return "<span class=\"glyphicon ".$glyphiconClass." glyphicon-menu\"></span>";
	}
	
	public function getTextEvent($user = null) {
		if (!$this -> type)
			return null;
		
		$textEvent = "undefined";
		
		switch ($this -> type) {
			case 'post-add':
				$textEvent = 'Nouvelle publication de '.$this -> post -> getAuthor() -> getUsername();
				break;
			case 'post-edit':
				$textEvent = 'Publication mise à jour par '.$this -> author -> getUsername();
				break;
			case 'comment-add':
				//if ($isGroupe)
				$textEvent = 'Nouveau commentaire de '.$this -> comment -> getAuthor() -> getUsername();
				if ($user && $user == $this -> post -> getAuthor())
					$textEvent.= ' sur votre publication';
				break;
		}
		return $textEvent;
	}
	
	public function getPathEvent($result = 'path-name') {
		if (!$this -> type && !$this -> getGroupes())
			return null;
		
		$listGroupes = $this -> getGroupes();
		$groupe = $listGroupes[0];
	
		if ($result == 'path-name')
			$path = "groupe";
		
		switch ($this -> type) {
			case 'post-add':
				$path[] = "'groupe_id' : '".$groupe -> getId()."'";
				$path[] = "'post_id' : '".$this -> getPost() -> getId()."'";
				if ($result == 'path-name')
					$path = "groupe";
				break;
			case 'post-edit':
				//$path = "groupe";
				break;
			case 'comment-add':
				$path[] = "'groupe_id' : '".$groupe -> getId()."'";
				$path[] = "'post_id' : '".$this -> getPost() -> getId()."'";
				if ($result == 'path-name')
					$path = "groupe";
				break;
		}
		return $path;
	}
	
	

}
