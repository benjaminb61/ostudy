<?php
namespace AppBundle\Services;

//use AppBundle\Mailer;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class AuthManager
{
	
    /**
     * @var EntityManager 
     */
    protected $em;
	
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    public function __construct(\Doctrine\ORM\EntityManager $em, TokenStorage $tokenStorage)
    {
        $this->em = $em;
		$this->tokenStorage = $tokenStorage;
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
	
	public function checkMemberGroupe($groupe) {
		if ($groupe) {
			//$groupe = $this->em-> getRepository('AppBundle:Groupe') -> find($groupe_id);
			if ($this -> getUser() && $groupe -> checkMember($this -> getUser()))
				return true;
		}
		return false;
	}
	
	public function checkAuthAccessGroupe($groupe) {
		if ($groupe) {
			//$repository = $this->em-> getRepository('AppBundle:Groupe');
			//if ($repository -> getGroupeByIdAndUser($groupe_id, $this -> getUserId()))
			if ($groupe -> getStatut() == 2)
				return true;
			if ($this -> getUser() && $groupe -> checkMember($this -> getUser()))
				return true;
		}
		return false;
	}
	
	public function checkAuthAdminGroupe($groupe) {
		if ($groupe) {
			//$repository = $this->em-> getRepository('AppBundle:Groupe');
			//if ($groupe = $repository -> getGroupeByIdAndUser($groupe_id, $this -> getUserId()))
				if ($this -> getUser() && $groupe -> checkAdministrator($this -> getUser()))
					return true;
		}
		return false;
	}
	
	public function checkAuthPostGroupe($groupe) {
		if ($groupe) {
			//$repository = $this->em-> getRepository('AppBundle:Groupe');
			//if ($groupe = $repository -> getGroupeByIdAndUser($groupe_id, $this -> getUserId()))
				if ($groupe -> checkAdministrator($this -> getUser()) || ($groupe -> getAuthPost() == 1))
					return true;
		}
		return false;
	}

	public function test() {
		return "essai";
	}

    // ...
}