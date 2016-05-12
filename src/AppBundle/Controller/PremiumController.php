<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PremiumController extends Controller
{
    /**
     * @Route("/premium", name="premium")
     */
    public function indexAction(Request $request)
    {
		$user = $this->getUser();
			
        return $this->render('default/index.html.twig', array(
            'base_dir' => 'je taime ma bébé choute',
        ));
    }
	
	public function menuAction() {
		$user = $this->getUser();
		if ($user == null) {
			return $this->render('default/menu/menu.html.twig', array(
			  'listGroupes' => null,
			  'menu_title' => 'Mes groupes de travail'
			));
		}
		$repository = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('AppBundle:Groupe')
		;
		
		$listGroupes = $repository->getGroupesByUser($user -> getId());
	
		return $this->render('default/menu/menu.html.twig', array(
		  'listGroupes' => $listGroupes,
		  'menu_title' => 'Mes groupes de travail'
		));
	
	}
	
    /**
     * @Route("/bebe/choute", name="bébéchoute")
     */
    public function blablaAction()
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => 'je taime ma bébé choute',
        ));
    }
}
