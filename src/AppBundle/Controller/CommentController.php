<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use AppBundle\Form\Type\CommentType;
//use Perso\BlogBundle\Form\ArticleType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class CommentController extends Controller
{
    public function adminAction($page)
    {
		if ($page < 1) {
		  throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		$nbPerPage = 20;
		// Pour récupérer la liste de toutes les annonces : on utilise findAll()
		$listComments = $this->getDoctrine()
		  ->getManager()
		  ->getRepository('PersoBlogBundle:Comment')
		  ->getComments($page, $nbPerPage, $admin = true)
		;
		
		$nbPages = ceil(count($listComments) / $nbPerPage);
		
		// Si la page n'existe pas, on retourne une 404
		if ($page > $nbPages) {
		  throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
        return $this->render('PersoBlogBundle:Admin:comments.html.twig', array(
			'listComments' => $listComments,
			'nbPages' => $nbPages,
			'page' => $page
		));
    }
	public function editAction($id, Request $request) {
		$em = $this->getDoctrine()->getManager();
		$comment = $em
			->getRepository('PersoBlogBundle:Comment')
			->find($id)
		;
	
		// On crée le FormBuilder grâce au service form factory
		$form = $this->get('form.factory')->create(new CommentType(), $comment);
	
		$form->handleRequest($request);
		if ($form->isValid()) {
		  // On l'enregistre notre objet $advert dans la base de données, par exemple
		  $em = $this->getDoctrine()->getManager();
		  $em->persist($comment);
		  $em->flush();
	
		  $request->getSession()->getFlashBag()->add('msg_success', 'Le commentaire a bien été modifié !');
	
		  return $this->redirect($this->generateUrl('perso_blog_admin_comments'));
		}
	
		return $this->render('PersoBlogBundle:Admin:edit-comment.html.twig',array(
		'form' => $form->createView()));
	}
	
    /**
     * @Route("/comment/delete/{comment_id}", name="comment-delete")
     */
	
	public function deleteAction($comment_id, Request $request) {
		if (!$request->isXmlHttpRequest()) {
			throw new \Exception('Only Ajax request!');
		}
	
		$em = $this->getDoctrine()->getManager();
		$comment = $em->getRepository('AppBundle:Comment')->find($comment_id);
		
		if ($comment == null) {
		  throw $this->createNotFoundException("Le commentaire d'id ".$comment_id." n'existe pas.");
		}
	
		$em->remove($comment);
		$em->flush();
		
		$response = json_encode(array('message' => 'Le commentaire a bien été supprimé'));

		return new Response($response, 200, array(
			'Content-Type' => 'application/json'
		));
	}
	
    /**
     * @Route("/comment/add/{post_id}", name="comment-add")
     */
	
	public function addAction($post_id, Request $request) {
		$user = $this->getUser();

		if ($request -> query -> get('groupe_id')) {
			$groupe_id = $request -> query -> get('groupe_id');
			
			$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');
			$groupe = $repository->find($groupe_id);
			
			$authService = $this->container->get('app.auth_manager');
			if (!$authService -> checkAuthAccessGroupe($groupe)) {
				throw new \Exception('Impossible d\'accéder à ce groupe');
			}
		}
		else {
			$groupe_id = null;
			$groupe = null;
		}
		
		$em = $this->getDoctrine()->getManager();
		$post = $em->getRepository('AppBundle:Post')->find($post_id);
		
		$comment = new Comment();
		$comment 
			-> setPost($post)
			-> setAuthor($user);
		
		$form_comment = $this->container->get('form.factory')->createNamed('add_comment_'.$post_id, CommentType::class, $comment);
		
		$form_comment->handleRequest($request);
		if ($request->isXmlHttpRequest()) {
			if (!$form_comment -> isValid()) {
				$response = json_encode(array('message' => 'Votre participation n\'est pas valide'));
				return new Response($response, 419, array(
					'Content-Type' => 'application/json'
				));
			}
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($comment);
			$em->flush();
	
			$response = json_encode(array('comment_id' => $comment->getId(), 'content' => $comment->getContent(), 'author' => $comment->getAuthor()->getUsername(), 'date' => $comment->getDate()->format('d/m/Y'), 'heure' => $comment->getDate()->format('H:i'), 'path_delete' => $this->generateUrl('comment-delete', array('comment_id' => $comment -> getId(), 'groupe_id' => $groupe_id))));

			return new Response($response, 200, array(
				'Content-Type' => 'application/json'
			));
		}
	
		return $this->render('default/form/form-comment.html.twig', array(
		  'form_comment' => $form_comment->createView(),
		  'post_id' => $post_id,
		  'groupe_id' => $groupe_id
		));
	
	}
}
