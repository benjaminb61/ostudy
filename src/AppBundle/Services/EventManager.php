<?php
namespace AppBundle\Services;

//use AppBundle\Mailer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

use AppBundle\Entity\Event;

class EventManager
{
	
    /**
     * @var EntityManager 
     */
    protected $em;
	
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;
	
	/**
     * @var Mailer
     */
    protected $mailer;

    public function __construct(\Doctrine\ORM\EntityManager $em, TokenStorage $tokenStorage, \Swift_Mailer $mailer)
    {
        $this->em = $em;
		$this->tokenStorage = $tokenStorage;
		$this->mailer = $mailer;
    }
	
    public function getUser()
    {
		if (null === $token = $this->tokenStorage->getToken()) {
			// no authentication information is available
			return;
		}
		if (!is_object($user = $token->getUser())) {
			// e.g. anonymous authentication
			return;
		}
        return $user;
    }
    public function getUserId()
    {
		if ($this -> getUser())
			return $this -> getUser() -> getId();
		return false;
    }
	
	public function addEvent($type,\AppBundle\Entity\Groupe $groupe = null, \AppBundle\Entity\Post $post = null, \AppBundle\Entity\Comment $comment = null) {
		$event = new Event();
		/*if ($type == 'post-add' && ($groupe && $post)) {
			if ($post -> getGroupes()) {
				foreach ($post -> getGroupes() as $groupe) {
					$event ->addGroupe($groupe);
					$groupe -> updateLastUpdate();
					$this->em -> persist($groupe);
				}
			}
			$event ->setPost($post);
		}
		if ($type == 'post-edit' && ($groupe && $post)) {
			
			if ($post -> getGroupes()) {
				foreach ($post -> getGroupes() as $groupe) {
					$event ->addGroupe($groupe);
					$groupe -> updateLastUpdate();
					$this->em -> persist($groupe);
				}
			}
			$this->em -> persist($post);
			
			$event ->setPost($post);
		}*/
		/*if ($type == 'comment-add' && ($comment && $post)) {
			/*if ($this -> getUser() == $comment -> getAuthor())
				return;*/
			/*if ($post -> getGroupes()) {
				foreach ($post -> getGroupes() as $groupe) {
					$event ->addGroupe($groupe);
					$groupe -> updateLastUpdate();
					$this->em -> persist($groupe);
				}
			}
			
			$event 
				->setComment($comment)
				->setPost($post);
			
			foreach ($post -> getComments() as $com) {
				if ((!in_array($com -> getAuthor(),array($event -> getUsers()))) && ($com -> getAuthor() != $this -> getUser()))
					$event ->addUser($com -> getAuthor());
			}
			if ($post -> getAuthor() != $this -> getUser())
				$event ->addUser($post -> getAuthor());
			
		}
		$event
			->setType($type)
			->setAuthor($this -> getUser());
		
		$this->em -> persist($event);
		$this->em -> flush();*/
	}

	public function test() {
		return "essai";
	}

    // ...
}