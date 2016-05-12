<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/{page}", defaults={"page" = 1}, name="homepage", requirements={
	 * 	"page": "\d+"
	 * })
     */
    public function indexAction($page, Request $request)
    {
		$tag_name = null;
		$user = $this->getUser();
		if ($user == null) {
			return $this->render('index.html.twig');
		}
		
		if ($page < 1) {
		  throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		$nbPerPage = 10;
		
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');

		$listPosts = $repository -> getPostByUser($user -> getId(),$tag_name = $request -> query -> get('tag_name'),$page,$nbPerPage);
		
		$nbPages = (is_array($listPosts)) ? ceil(count($listPosts) / $nbPerPage) : 1;
		if ($page > $nbPages && $page > 1) {
		  throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
			
        return $this->render('default/home/index.html.twig', array(
			'listPosts' => $listPosts,
			'tag_name' => $tag_name,
			'groupe_id' => null,
			'page' => $page,
			'nbPages' => $nbPages
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
