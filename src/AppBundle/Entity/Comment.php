<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	/**
	* @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
	* @ORM\JoinColumn(nullable=false)
	*/
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
	 * @Assert\NotBlank()
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
	 * @Assert\DateTime()
     */
    private $date;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isPublished", type="boolean")
	 * @Assert\NotBlank()
     */
    private $isPublished;

	/**
	* @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post", inversedBy="comments")
	* @ORM\JoinColumn(nullable=false)
	*/
	
	private $post;
	
	private $redirectUrl;

    /**
     * Get id
     *
     * @return integer 
     */
	 
	 
	public function __construct()
	{
		$this->date = new \Datetime();
		$this->isPublished = true;
	}
	
    public function getRedirectUrl()
    {
        return $this -> redirectUrl;
    }
    public function setRedirectUrl($url)
    {
        $this -> redirectUrl = $url;
    }
	
	
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set author
     *
     * @param \AppBundle\Entity\User $user
     * @return Comment
     */
    public function setAuthor(\AppBundle\Entity\User $user)
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

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Comment
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
     * Set isPublished
     *
     * @param boolean $isPublished
     * @return Comment
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    
        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean 
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }
	
	public function viewStatut() {
		if ($this->published)
			return "<span style=\"font-weight:bold; color:green;\">Publié</span>";
		return "<span style=\"font-weight:bold; color:orange;\">Non publié</span>";
	}

	
	/*
	* @Assert\Callback(methods={"isContentValid"})
	*/
	public function isContentValid(ExecutionContextInterface $context)
	{
		$forbiddenWords = array('connard', 'blaireau');
	
		// On vérifie que le contenu ne contient pas l'un des mots
		if (preg_match('#'.implode('|', $forbiddenWords).'#', $this->getComment())) {
		  // La règle est violée, on définit l'erreur
		  $context
			->buildViolation('Contenu invalide car il contient un mot interdit.') // message
			->atPath('comment')                                                   // attribut de l'objet qui est violé
			->addViolation() // ceci déclenche l'erreur, ne l'oubliez pas
		  ;
		}
	}

    /**
     * Set post
     *
     * @param \AppBundle\Entity\Post $post
     *
     * @return Comment
     */
    public function setPost(\AppBundle\Entity\Post $post)
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
     * Set content
     *
     * @param string $content
     *
     * @return Comment
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
}
