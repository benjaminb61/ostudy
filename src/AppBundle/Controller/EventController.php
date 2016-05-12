<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Event;
use AppBundle\Entity\Post;
//use AppBundle\Form\Type\CommentType;
//use Perso\BlogBundle\Form\ArticleType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class EventController extends Controller
{
	public function viewAction(Request $request) {
		$user = $this->getUser();
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Event');

		$listEvents = $repository -> getEventByUser($user -> getId());

		//return new Response('-'.sizeof($listEvents).'-');
	
		return $this->render('default/event/notifications.html.twig',array(
		'listEvents' => $listEvents));
	}
}
