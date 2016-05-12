<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Post
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
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
	 * @Assert\Length(min=10)
     */
    private $content;

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
     * @ORM\Column(name="lastUpdate", type="datetime", nullable=false)
	 * @Assert\DateTime()
     */
    private $lastUpdate;


    /**
     * @var int
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;
	
	/**
	 * @ORM\OneToOne(targetEntity="AppBundle\Entity\Document", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
	 */
	private $document;

    /**
     * @var int
     *
     * @ORM\Column(name="statut", type="integer")
     */
    private $statut;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="text", nullable=true)
     */
    private $tag;
	
	/**
	* @ORM\ManyToMany(targetEntity="AppBundle\Entity\Groupe", cascade={"persist"})
	*/
	private $groupes;
	
	/**
	* @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="post", cascade={"persist", "remove"})
	* @ORM\OrderBy({"date" = "ASC"})
	* @Assert\Valid()
	*/
	
	private $comments;
	
    /**
     * @var int
     *
     * @ORM\Column(name="vote", type="integer", nullable=true)
     */
    private $vote = 0;
	
    /**
     * @var int
     *
     * @ORM\Column(name="favorite", type="integer", nullable=true)
     */
    private $favorite = 0;
	
	/**
	* @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", cascade={"persist"})
	* @ORM\JoinTable(name="post_voting")
	* @Assert\Valid()
	*/
	
	private $voting;
	
	/**
	* @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", cascade={"persist"})
	* @ORM\JoinTable(name="post_favoriting")
	* @Assert\Valid()
	*/
	
	private $favoriting;
	
	
	public function __construct() {
		$this->date = new \Datetime();
		$this->lastUpdate = new \Datetime();
		$this->statut = 1;
		$this->groupes = new ArrayCollection();
		$this->voting = new ArrayCollection();
		$this->favoriting = new ArrayCollection();
		$this->uploadedFiles = new ArrayCollection();
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
     * Set type
     *
     * @param string $type
     *
     * @return Post
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
     * Set title
     *
     * @param string $title
     *
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Post
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
	
	/*public function getContentTwig() {
		$content = preg_replace('/(^|\s)#((\w|\s|[&;])*)#($|\s)/',' <span class="label label-info tag"><a href="{{ path(\'groupe\', {\'groupe_id\' : groupe.id, \'tag_name\': tag}) }}">$2</a></span> ',$this -> content);
		//$content = preg_replace('/(^|\s)#\(((.*))\)/',' <a href="">$2</a> ',$this -> content);
		$content = preg_replace('/(^|\s)#(\w*)($|\s)/',' <a href="">$2</a> ',$content);
		return $content;
	}*/

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Post
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
     * @return Post
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
     * Set author
     *
     * @param integer $author
     *
     * @return Post
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return int
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set statut
     *
     * @param integer $statut
     *
     * @return Post
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
     * Set tag
     *
     * @param string $tag
     *
     * @return Post
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Add groupe
     *
     * @param \AppBundle\Entity\Groupe $groupe
     *
     * @return Post
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
	
	public function getListTags() {
		if ($this -> tag) {
			$explode = explode(",", $this -> tag);
			$i = 0;
			foreach ($explode as $tag) {
				//$tag = str_replace(' ', '', $tag);
				$listTags[$i++] = trim($tag);
			}
			return $listTags;
		}
	}

    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return Post
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set vote
     *
     * @param integer $vote
     *
     * @return Post
     */
    public function setVote($vote)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return integer
     */
    public function getVote()
    {
        return $this->vote;
    }
	
	public function increaseVote()
	{
		$this->setVote($nbVote = $this->vote + 1);
		return $nbVote;
	}
	
	public function decreaseVote()
	{
		$this->setVote($nbVote = $this->vote - 1);
		return $nbVote;
	}
	
	public function addFavorite()
	{
		$this->setFavorite($nbFavorite = $this->favorite + 1);
		return $nbFavorite;
	}
	
	public function removeFavorite()
	{
		$this->setFavorite($nbFavorite = $this->favorite - 1);
		return $nbFavorite;
	}

    /**
     * Add voting
     *
     * @param \AppBundle\Entity\User $voting
     *
     * @return Post
     */
    public function addVoting(\AppBundle\Entity\User $voting)
    {
        $this->voting[] = $voting;

        return $this;
    }

    /**
     * Remove voting
     *
     * @param \AppBundle\Entity\User $voting
     */
    public function removeVoting(\AppBundle\Entity\User $voting)
    {
        $this->voting->removeElement($voting);
    }

    /**
     * Get voting
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVoting()
    {
        return $this->voting;
    }
	
	public function checkVoteUser($user_id) {
		if ($this -> voting) {
			foreach($this -> voting as $userVote) {
				if ($userVote -> getId() == $user_id)
					return true;
			}
		}
		return false;
	}
	
	public function checkFavoriteUser($user_id) {
		if ($this -> favoriting) {
			foreach($this -> favoriting as $userFavorite) {
				if ($userFavorite -> getId() == $user_id)
					return true;
			}
		}
		return false;
	}

    /**
     * Set favorite
     *
     * @param integer $favorite
     *
     * @return Post
     */
    public function setFavorite($favorite)
    {
        $this->favorite = $favorite;

        return $this;
    }

    /**
     * Get favorite
     *
     * @return integer
     */
    public function getFavorite()
    {
        return $this->favorite;
    }

    /**
     * Add favoriting
     *
     * @param \AppBundle\Entity\User $favoriting
     *
     * @return Post
     */
    public function addFavoriting(\AppBundle\Entity\User $favoriting)
    {
        $this->favoriting[] = $favoriting;

        return $this;
    }

    /**
     * Remove favoriting
     *
     * @param \AppBundle\Entity\User $favoriting
     */
    public function removeFavoriting(\AppBundle\Entity\User $favoriting)
    {
        $this->favoriting->removeElement($favoriting);
    }

    /**
     * Get favoriting
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFavoriting()
    {
        return $this->favoriting;
    }
	
	

	/*public function upload()
	{
		foreach($this->uploadedFiles as $uploadedFile)
		{
			$file = new File();


			$path = sha1(uniqid(mt_rand(), true)).'.'.$uploadedFile->guessExtension();
			$file->setPath($path);
			$file->setSize($uploadedFile->getClientSize());
			$file->setName($uploadedFile->getClientOriginalName());

			$uploadedFile->move($this->getUploadRootDir(), $path);

			$this->getFiles()->add($file);
			$file->setDocument($this);

			unset($uploadedFile);
		}
	}*/
}
