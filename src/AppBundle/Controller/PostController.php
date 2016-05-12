<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use AppBundle\Entity\User;
use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Document;

use AppBundle\Form\Type\CommentType;
use AppBundle\Form\Type\PostType;

class PostController extends Controller
{
	
    /**
     * @Route("/favorites/{page}", defaults={"page" = 1}, name="favorites", requirements={
	 * 	"page": "\d+"
	 * })
     */

    public function showPostFavorites($page, Request $request)
    {
		$tag_name = null;
		$user = $this->getUser();
		if ($user == null) {
			$request->getSession()->getFlashBag()->add('msg_error', 'Vous devez vous connecter pour accéder à cette zone');
			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}
		
		if ($page < 1) {
		  throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		$nbPerPage = 10;
		
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');
		
		$listPosts = $repository -> getPostFavoritesByUser($user -> getId(),$tag_name = $request -> query -> get('tag_name'),$page,$nbPerPage);
		
		$nbPages = (is_array($listPosts)) ? ceil(count($listPosts) / $nbPerPage) : 1;
		if ($page > $nbPages && $page > 1) {
		  throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
			
        return $this->render('default/favorites.html.twig', array(
			'listPosts' => $listPosts,
			'tag_name' => $tag_name,
			'groupe_id' => null,
			'page' => $page,
			'nbPages' => $nbPages
		));
    }
	
    /**
     * @Route("/groupe/{groupe_slug}/tags/", name="groupe-tags")
     */
    public function showTagByGroupe($groupe_slug, Request $request)
    {
		/*$authService = $this->container->get('app.auth_manager');
		if (!$authService -> checkAuthAccessGroupe($groupe_id)) {
			throw new \Exception('Impossible d\'accéder à ce groupe');
		}*/
		
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');
		$listPosts = $repository -> getPostByGroupe($groupe_slug);

		$chaineTags = null;
		$listTags = null;
		if ($listPosts) {
			foreach ($listPosts as $post) {
				//if (!preg_match("/\b".$post -> getTag()."\b/i", $chaineTags)) {

				$chaineTags.= $post -> getTag().',';
				//}
			}
			$chaineTags = rtrim($chaineTags, ', '); // Clean up end string
			if ($chaineTags) {
				$listTags = explode(',',$chaineTags);
				$listTags = array_intersect_key(
					$listTags,
					array_unique(array_map("StrToLower",$listTags))
				);
				$listTags = array_map('trim',$listTags);
			}
		}
        return $this->render('default/list-tags.html.twig', array(
			'listTags' => $listTags,
			'groupe_slug' => $groupe_slug
		));
    }
	
    /**
     * @Route("/favorites/tags", name="favorites-tags")
     */
    public function showTagByFavorites(Request $request)
    {
		/*$authService = $this->container->get('app.auth_manager');
		if (!$authService -> checkAuthAccessGroupe($groupe_id)) {
			throw new \Exception('Impossible d\'accéder à ce groupe');
		}
		*/
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');
		$listPosts = $repository -> getPostFavoritesByUser($this -> getUser() -> getId());
		$chaineTags = null;
		$listTags = null;
		if ($listPosts) {
			foreach ($listPosts as $post) {
				$chaineTags.= $post -> getTag().',';
			}
			$listTags = array_unique(explode(',',$chaineTags));
			$listTags = array_map('trim',$listTags);
		}
			
        return $this->render('default/list-tags.html.twig', array(
			'listTags' => $listTags,
			'groupe_id' => null
		));
    }
	
    /**
     * @Route("/post/add", name="post-add")
     */
	
    public function addAction(Request $request)
    {
		
		$post = new Post();
		$post -> setAuthor($this -> getUser());
		
		if ($request -> query -> get('groupe_id')) {
			$groupe_id = $request -> query -> get('groupe_id');
			
			$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');
			$groupe = $repository->find($groupe_id);
			
			if (!$groupe)
				throw $this->createNotFoundException("Ce groupe n'existe pas");
			
			$authService = $this->container->get('app.auth_manager');
			if (!$authService -> checkAuthPostGroupe($groupe)) {
				throw new \Exception('Impossible d\'accéder à ce groupe');
			}
			
			$post -> addGroupe($groupe);
		}
		else {
			$groupe_id = null;
		}
	
		// Formulaire Post
        $form_post = $this->createForm(PostType::class, $post);
		$form_post->handleRequest($request);
		
		if ($form_post->isSubmitted() && $form_post->isValid()) {

            // $file stores the uploaded PDF file
            /** Symfony\Component\HttpFoundation\File\UploadedFile $file */
            /*if ($post -> getDocument()) {
				$file = $post->getDocument();

				// Generate a unique name for the file before saving it
				$fileName = $file -> getClientOriginalName().'_'.md5(uniqid()).'.'.$file->guessExtension();

				// Move the file to the directory where brochures are stored
				$documentsDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/documents';
				$file->move($documentsDir, $fileName);

				// Update the 'brochure' property to store the PDF file name
				// instead of its contents
				$post->setDocument($fileName);
			}*/
			//$post -> upload();
			
		
			$em = $this->getDoctrine()->getManager();
			$em->persist($post);
			$em->flush();
		
			$request->getSession()->getFlashBag()->add('msg_success', 'Votre contribution a bien été ajoutée sur oStudy ;-)');
			
			if ($groupe_id)
				return $this->redirect($this->generateUrl('groupe', array('groupe_slug' => $groupe -> getSlug(), 'post_id' => $post -> getId())) . '#post_'.$post -> getId());
			return $this->redirect($this->generateUrl('homepage'));
		}
			
        return $this->render('default/form/form-post.html.twig', array(
			'form' => $form_post->createView(),
			'groupe_id' => $groupe_id,
			'post_id' => null
		));
    }
	
    /**
     * @Route("/post/edit/{post_id}", name="post-edit")
     */
	
	public function editAction($post_id, Request $request) {
		
		if ($request -> query -> get('groupe_id')) {
			$groupe_id = $request -> query -> get('groupe_id');
			
			$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');
			$groupe = $repository->find($groupe_id);
			
			if (!$groupe)
				throw $this->createNotFoundException("Ce groupe n'existe pas");
		}
		else {
			$groupe_id = null;
		}
		
		$em = $this->getDoctrine()->getManager();
		$post = $em->getRepository('AppBundle:Post')->find($post_id);
		
		if ($post == null)
		  throw $this->createNotFoundException("Le post id ".$id." n'existe pas.");
		
		if (($post -> getAuthor() != $this->getUser()) && !$groupe -> checkAdministrator($this -> getUser())) {
			$request->getSession()->getFlashBag()->add('msg_error', 'Vous n\'êtes pas autorisé à effectuer cette action');
			if ($groupe_id)
				return $this->redirect($this->generateUrl('groupe', array('groupe_id' => $groupe_id)));	
			return $this->redirect($this->generateUrl('homepage'));	
		}
		
		$form = $this->createForm(PostType::class, $post);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$post -> updateLastUpdate();
			$em->persist($post);
			$em->flush();
	
			$request->getSession()->getFlashBag()->add('msg_success', 'Le post a correctement été modifié.');

			if ($groupe) 
				return $this->redirect($this->generateUrl('groupe', array('groupe_slug' => $groupe -> getSlug(), 'post_id' => $post -> getId())) . '#post_'.$post -> getId());
			return $this->redirect($this->generateUrl('homepage'));	
		}
	
        return $this->render('default/form/form-post.html.twig', array(
			'form' => $form->createView(),
			'groupe_id' => $groupe_id,
			'post_id' => $post_id
		));
	}
	
    /**
     * @Route("/post/delete/{post_id}", name="post-delete")
     */
	
	public function deleteAction($post_id, Request $request) {
		
		if ($request -> query -> get('groupe_id')) {
			$groupe_id = $request -> query -> get('groupe_id');
			
			$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');
			$groupe = $repository->find($groupe_id);
			
			if (!$groupe)
				throw $this->createNotFoundException("Ce groupe n'existe pas");
		}
		else {
			$groupe_slug = null;
		}
		
		$em = $this->getDoctrine()->getManager();
		$post = $em->getRepository('AppBundle:Post')->find($post_id);
		
		if ($post == null) {
		  throw $this->createNotFoundException("Le post id ".$id." n'existe pas.");
		}
		
		if ($post -> getAuthor() != $this->getUser() && !$groupe -> checkAdministrator($this -> getUser())) {
			$request->getSession()->getFlashBag()->add('msg_error', 'Vous n\'êtes pas autorisé à effectuer cette action');
			if ($groupe_slug)
				return $this->redirect($this->generateUrl('groupe', array('groupe_slug' => $groupe -> getSlug())));	
			return $this->redirect($this->generateUrl('homepage'));	
		}
			
		/*if ($post->getDocument()) {
			$documentsDir = $this->container->getParameter('kernel.root_dir').'/../web/uploads/documents/';
            unlink($documentsDir.$post -> getDocument());
		}*/
		$em->remove($post);
		$em->flush();
	
		$request->getSession()->getFlashBag()->add('msg_success', 'Le post a correctement été supprimé.');

		if ($groupe_id)
			return $this->redirect($this->generateUrl('groupe', array('groupe_slug' => $groupe -> getSlug())));	
		return $this->redirect($this->generateUrl('homepage'));	
	}
	
    /**
     * @Route("/post/top/{post_id}", name="post-top")
     */
	
	public function topAction($post_id, Request $request) {
		
		if ($request -> query -> get('groupe_id')) {
			$groupe_id = $request -> query -> get('groupe_id');
			
			$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');
			$groupe = $repository->find($groupe_id);
			
			if (!$groupe)
				throw $this->createNotFoundException("Ce groupe n'existe pas");
		}
		else {
			$groupe_id = null;
		}
		
		$em = $this->getDoctrine()->getManager();
		$post = $em->getRepository('AppBundle:Post')->find($post_id);
		
		if ($post == null) {
		  throw $this->createNotFoundException("Le post id ".$id." n'existe pas.");
		}
		
		if (!$groupe -> checkAdministrator($this->getUser())) {
			$request->getSession()->getFlashBag()->add('msg_error', 'Vous n\'êtes pas autorisé à effectuer cette action');
			if ($groupe_id)
				return $this->redirect($this->generateUrl('groupe', array('groupe_id' => $groupe_id)));	
			return $this->redirect($this->generateUrl('homepage'));	
		}
		$newStatut = 2;
		if ($post -> getStatut() == '2')
			$newStatut = 1;
		
		$post -> setStatut($newStatut);

		$em->persist($post);
		$em->flush();
	
		$request->getSession()->getFlashBag()->add('msg_success', 'La publication a bien été placée en première position.');
		
		if ($newStatut == 1)
			$request->getSession()->getFlashBag()->set('msg_success', 'La publication a bien été retirée de la première position.');

		if ($groupe_id)
			return $this->redirect($this->generateUrl('groupe', array('groupe_id' => $groupe_id)));	
		return $this->redirect($this->generateUrl('homepage'));	
	}
	
    /**
     * @Route("/post/{post_id}/vote/{action}", name="post-vote")
     */
	
    public function voteAction($post_id, $action = 'add', Request $request)
    {
		if($request->isXmlHttpRequest()) {
			$user = $this->getUser();
			if ($user == null) {
				$request->getSession()->getFlashBag()->add('msg_error', 'Vous devez vous connecter pour accéder à cette zone');
				return $this->redirect($this->generateUrl('fos_user_security_login'));
			}
			/*if ($page < 1) {
			  throw $this->createNotFoundException("La page ".$page." n'existe pas.");
			}
			$nbPerPage = 15;*/
			
			$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');
			
			/*$check = $repository->checkVotePostByUser($this -> GetUser() -> getId(),$post_id);
			return new Response(sizeof($check));*/
			$post = $repository->find($post_id);
			
			$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');
			$author = $repository->find($post -> getAuthor() -> getId());
			
			if (!$post || $post -> getAuthor() == $user) {
				$response = json_encode(array('message' => 'Vous ne pouvez pas voter pour ce message'));
				return new Response($response, 419, array(
					'Content-Type' => 'application/json'
				));
			}
		
			if ($action == 'add') {
				$post -> addVoting($user);
				$post -> increaseVote();	
				$author -> increaseSumVote();				
			}
			else if ($action == 'remove') {
				$post -> removeVoting($user);
				$post -> decreaseVote();
				$author -> decreaseSumVote();					
			}
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($post);
			$em->persist($author);
			$em->flush();
			
			return new Response(sizeof($post));
			//return new Response("Votre vote a bien été pris en compte");
		}	
    }
	
    /**
     * @Route("/post/{post_id}/favorite/{action}", name="post-favorite")
     */
	
    public function favoriteAction($post_id, $action = 'add', Request $request)
    {
		if($request->isXmlHttpRequest()) {
			$user = $this->getUser();
			if ($user == null) {
				$request->getSession()->getFlashBag()->add('msg_error', 'Vous devez vous connecter pour accéder à cette zone');
				return $this->redirect($this->generateUrl('fos_user_security_login'));
			}
			
			$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');
			
			/*$check = $repository->checkVotePostByUser($this -> GetUser() -> getId(),$post_id);
			return new Response(sizeof($check));*/
			$post = $repository->find($post_id);
			
			if (!$post) {
				$response = json_encode(array('message' => 'Vous ne pouvez pas ajouter ce message à vos favoris'));
				return new Response($response, 419, array(
					'Content-Type' => 'application/json'
				));
			}
		
			if ($action == 'add') {
				$post -> addFavoriting($user);
				$post -> addFavorite();			
			}
			else if ($action == 'remove') {
				$post -> removeFavoriting($user);
				$post -> removeFavorite();		
			}
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($post);
			$em->flush();
			
			//return new Response(sizeof($post));
			return new Response("Favoris bien enregistré");
		}	
    }
}
