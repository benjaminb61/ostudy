<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\User;
use AppBundle\Entity\Post;
use AppBundle\Entity\Groupe;
use AppBundle\Entity\UserGroupe;

use AppBundle\Form\Type\GroupeAddType;
use AppBundle\Form\Type\GroupeType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class GroupeController extends Controller
{
	
	public function navAction() {
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
	
		return $this->render('default/menu/nav-groupes.html.twig', array(
		  'listGroupes' => $listGroupes,
		));
	
	}
	
	public function menuAction($groupe) {
		$check_admin = false;
		$check_member = false;
		$repository = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('AppBundle:Groupe')
		;
		
		$groupe = $repository->find($groupe -> getId());
		
		$authService = $this->container->get('app.auth_manager');
		if ($authService -> checkAuthAdminGroupe($groupe))
			$check_admin = true;
		if ($authService -> checkMemberGroupe($groupe))
			$check_member = true;
		
		$repository = $this
		  ->getDoctrine()
		  ->getManager()
		  ->getRepository('AppBundle:Event')
		;	
		$listEvents = $repository->getEventByGroupe($groupe -> getId());
		
		//return new Response('-'.sizeof($listEvents).'-');
	
		return $this->render('default/menu/menu-groupe.html.twig', array(
		  'groupe' => $groupe,
		  'check_admin' => $check_admin,
		  'check_member' => $check_member,
		  'listEvents' => $listEvents
		));
	
	}
	
    /**
     * @Route("/groupe/add", name="groupe-add")
     */
	
	public function addAction(Request $request) {
		$user = $this->getUser();
		
		$groupe = new Groupe();

		$form_groupe = $this->createForm(GroupeAddType::class, $groupe);
		//$form_comment = $this->container->get('form.factory')->createNamed('add_comment_'.$post_id, CommentType::class, $comment);
		
		$form_groupe->handleRequest($request);
		if ($form_groupe->isSubmitted() && $form_groupe->isValid()) {
			
			$userGroupe = new UserGroupe();
			$userGroupe 
				-> setUser($user)
				-> setIsAdministrator(1)
				-> setGroupe($groupe);
		
			$groupe 
				-> setFounder($user)
				-> addUserGroupe($userGroupe);
			
			$char = 'abcdefghijklmnopqrstuvwxyz0123456789';
			$char_shuffle = str_shuffle($char);
			$groupe -> setCode($char_shuffle);
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($groupe);
			$em->flush();
			
			$post_first = new Post();
			$post_first
				->setStatut(2)
				->setAuthor($user)
				->addGroupe($groupe)
				->setTitle("Votre première publication")
				->setContent("<p><strong>F&eacute;licitation, votre groupe vient d&#39;&ecirc;tre cr&eacute;&eacute; avec <u>succ&egrave;s</u> !</strong></p><p>&nbsp;</p><p>Voici un exemple de publication en <em>top position</em>, indentifi&eacute;e par le fond color&eacute; et le logo <span class=\"label label-warning\">Top !</span>. Cette publication restera au dessus de toutes les autres, elle peut faire office de consigne, de sommaire, de plan, de pr&eacute;sentation...</p><p>&nbsp;</p><p>Chaque publication est param&egrave;trable depuis le bouton <button type=\"button\" class=\"btn btn-default dropdown-toggle btn-sm\"><span class=\"glyphicon glyphicon-option-vertical\" style=\"font-size:16px;\"></span></button> situ&eacute;e sur la gauche du texte.</p><p>&nbsp;</p><p>Vous pouvez faire des liens vers vos différents tags : par exemple vers le tag #sommaire#. Il vous suffit alors d&#39;entourer votre mot du signe &quot;#&quot;</p>")
				->setTag("top publication,sommaire")
			;
			
			$post_second = new Post();
			$post_second
				->setAuthor($user)
				->addGroupe($groupe)
				->setTitle("Votre seconde publication : utilisation de votre groupe")
				->setContent("<p>Voici votre seconde publication, en dessous de votre sommaire ou de votre plan</p><p>&nbsp;</p><p>Par d&eacute;faut, votre groupe est <strong>accessible uniquement sur invitation</strong>. Vous pouvez modifier les param&egrave;tres de votre groupe sur la <a href=\"/edit\" title=\"Pr&eacute;f&eacute;rences du groupe\">page pr&eacute;f&eacute;rences</a>.</p><p>&nbsp;</p><p>Vous pouvez <strong>inviter des membres</strong> pour commencer &agrave; faire conna&icirc;tre votre groupe de travail. Les membres invit&eacute;s peuvent par d&eacute;faut poster des commentaires et de nouvelles publications. Ces param&egrave;tres peuvent aussi &ecirc;tre modifi&eacute;s sur la <a href=\"/edit\" title=\"Pr&eacute;f&eacute;rences du groupe\">page pr&eacute;f&eacute;rences</a>.</p>")
				->setTag("seconde publication")
			;
			
			$em->persist($post_second);
			$em->persist($post_first);
			$em->flush();
		
			$request->getSession()->getFlashBag()->add('msg_success', 'Bienvenue dans votre nouveau groupe <strong>'.$groupe -> getName().'</strong>');
		
			// On redirige vers la page de visualisation du groupe nouvellement créé
			return $this->redirect($this->generateUrl('groupe', array('groupe_slug' => $groupe -> getSlug())));
			//return $this -> redirect($request->getUri());
		}
	
		return $this->render('default/form/form-groupe-add.html.twig', array(
		  'form' => $form_groupe->createView(),
		  'name_path_action' => 'groupe-add'
		));
	
	}
	
    /**
     * @Route("/groupe/{groupe_slug}/edit", name="groupe-edit")
     */
	
	public function editAction($groupe_slug,Request $request) {
		$user = $this->getUser();
		
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');
		$groupe = $repository->findOneBySlug($groupe_slug);
		
		$authService = $this->container->get('app.auth_manager');
		if (!$authService -> checkAuthAdminGroupe($groupe)) {
			throw new \Exception('Impossible d\'accéder à ce groupe');
		}

		$form = $this->createForm(GroupeType::class, $groupe);
		//$form = $this->createFormBuilder()->getForm();
		
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($groupe);
			$em->flush();
		
			$request->getSession()->getFlashBag()->add('msg_success', 'Le groupe a correctement été modifié');
		
			// On redirige vers la page de visualisation du groupe nouvellement créé
			return $this->redirect($this->generateUrl('groupe', array('groupe_slug' => $groupe -> getSlug())));
			//return $this -> redirect($request->getUri());
		}
	
		return $this->render('default/groupe/groupe-edit.html.twig', array(
		  'form' => $form->createView(),
		  'groupe' => $groupe,
		  'name_path_action' => 'groupe-edit'
		));
	
	}
	
    /**
     * @Route("/groupe/{groupe_slug}/delete", name="groupe-delete")
     */
	
	public function deleteAction($groupe_slug,Request $request) {
		$user = $this->getUser();
		if ($user == null) {
			return $this->render('default/index.html.twig');
		}
		
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');
		$groupe = $repository->findOneBySlug($groupe_slug);

		//$form = $this->createForm(GroupeType::class, $groupe);
		$form = $this->createFormBuilder()->getForm();
		
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			
			$em = $this->getDoctrine()->getManager();
			$em->remove($groupe);
			
            $repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');
			$listPosts = $repository -> getPostByGroupe($groupe -> getSlug());
			foreach ($listPosts as $post) {
				$em-> remove($post);
			}
			
			$em->flush();
		
			$request->getSession()->getFlashBag()->add('msg_success', 'Le groupe a correctement été supprimé');
		
			// On redirige vers la page de visualisation du groupe nouvellement créé
			return $this->redirect($this->generateUrl('homepage'));
			//return $this -> redirect($request->getUri());
		}
	
		return $this->render('default/groupe/groupe-delete.html.twig', array(
		  'form' => $form->createView(),
		  'groupe' => $groupe
		));
	
	}
	
    /**
     * @Route("/groupe/{groupe_slug}/logout", name="groupe-logout")
     */
	
	public function logoutAction($groupe_slug,Request $request) {
		$user = $this->getUser();
		
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');
		$groupe = $repository->findOneBySlug($groupe_slug);

		//$form = $this->createForm(GroupeType::class, $groupe);
		$form = $this->createFormBuilder()->getForm();
		
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');
			$founder = $repository->find($groupe -> getFounder() -> getId());
			//return new Response('-'.$groupe -> deleteUser($user).'-'.$groupe -> deleteUser($user).'-');
			$groupe -> deleteUser($user);
			$founder -> decreaseSumMembers();
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($founder);
			$em->persist($groupe);
			$em->flush();
		
			$request->getSession()->getFlashBag()->add('msg_success', 'Vous venez de quitter le groupe <strong>'. $groupe -> getName() .'</strong>');
		
			// On redirige vers la page de visualisation du groupe nouvellement créé
			return $this->redirect($this->generateUrl('homepage'));
			//return $this -> redirect($request->getUri());
		}
	
		return $this->render('default/groupe/groupe-logout.html.twig', array(
		  'form' => $form->createView(),
		  'groupe' => $groupe
		));
	
	}
	
    /**
     * @Route("/groupe/{groupe_slug}/members", name="groupe-members")
     */
	
	public function membersAction($groupe_slug,Request $request) {
		$user = $this->getUser();
		
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');
		$groupe = $repository->findOneBySlug($groupe_slug);
		
		$authService = $this->container->get('app.auth_manager');
		if (!$authService -> checkAuthAccessGroupe($groupe)) {
			throw new \Exception('Impossible d\'accéder à ce groupe');
		}
		
		return $this->render('default/groupe/members.html.twig', array(
		  'listMembers' => $groupe->getUserGroupes(),
		  'groupe' => $groupe
		));
	
	}
	
    /**
     * @Route("/groupe/join/{code_groupe}", name="groupe-join")
     */
	
	public function joinAction($groupe_id = null, $code_groupe = null, Request $request) {
		$user = $this->getUser();

		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');

		if ($code_groupe) {
			$groupe = $repository->findOneBy(array('code' => $code_groupe));
		}
		else if ($groupe_id) {
			$groupe = $repository->find($groupe_id);
		}
		else {
			$groupe = new Groupe();
		}
		
		$formBuilder = $this->createFormBuilder();
		$formBuilder
		  ->add('code', TextType::class)
		  ->add('save', SubmitType::class, array('label' => 'Rejoindre'))

		;
		$form = $formBuilder -> getForm();
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$code_groupe = $request->request->get('form')['code'];
			$groupe = $repository->findOneBy(array('code' => $code_groupe));
			
			$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');
			$founder = $repository->find($groupe -> getFounder() -> getId());
			
			if (!$groupe) {
				$request->getSession()->getFlashBag()->add('msg_error', 'Aucun groupe ne correspond à ce code d\'adhésion <strong>'.$code_groupe.'</strong>');
				return $this->redirect($this->generateUrl('homepage'));
			}
			$userGroupe = new UserGroupe();	
			$userGroupe 
				-> setGroupe($groupe)
				-> setUser($this -> getUser());
			
			$groupe -> addUserGroupe($userGroupe);
			$founder -> increaseSumMembers();
			
			$em = $this->getDoctrine()->getManager();
			$em->persist($founder);
			$em->persist($groupe);
			$em->flush();
		
			$request->getSession()->getFlashBag()->add('msg_success', 'Bienvenue dans le groupe <strong>'.$groupe -> getName().'</strong>');
		
			// On redirige vers la page de visualisation du groupe nouvellement créé
			return $this->redirect($this->generateUrl('groupe', array('groupe_slug' => $groupe -> getSLug())));
			//return $this -> redirect($request->getUri());
		}
	
		return $this->render('default/form/form-groupe-join.html.twig', array(
		  'form' => $form->createView(),
		  'groupe_id' => $groupe_id,
		  'code_groupe' => $code_groupe
		));
	
	}
	
    /**
     * @Route("/groupe/{groupe_slug}/{page}", defaults={"page" = 1}, name="groupe", requirements={
	 * 	"page": "\d+"
	 * })
     */
    public function indexAction($groupe_slug, $page = 1, Request $request)
    {
		$tag_name = null;
		$check_admin = null;
		$check_member = null;
		$user_id = null;
		$user = null;

		if (is_object($this->getUser())) {
			$user = $this->getUser();
			$user_id =  $this->getUser() -> getId();
		}
		if ($page < 1) {
		  throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		$nbPerPage = 10;
		
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Groupe');
		$groupe = $repository->getGroupeBySlugAndUser($groupe_slug,$user_id);

		$authService = $this->container->get('app.auth_manager');
		if (!$authService -> checkAuthAccessGroupe($groupe)) {
			throw new \Exception('Impossible d\'accéder à ce groupe');
		}
		//return new Response($authService -> getUser() -> getUsername());
		
		$repository = $this->getDoctrine()->getManager()->getRepository('AppBundle:Post');
		
		//$this -> checkAuthAccessGroupe($groupe_id,$user -> getId());
		
		$listPosts = $repository -> getPostByGroupe($groupe -> getSlug(), $authService -> getUserId(), $tag_name = $request -> query -> get('tag_name'), $page, $nbPerPage);
		
		$nbPages = (is_object($listPosts)) ? (ceil(count($listPosts) / $nbPerPage)) : 1;
		//return new Response('-'.is_object($listPosts).'-');
		if ($page > $nbPages && $page > 1) {
		  throw $this->createNotFoundException("La page ".$page." n'existe pas.");
		}
		
		//return new Response(htmlentities($tag_name,ENT_QUOTES));
		
		if (!$groupe)
			throw $this->createNotFoundException("Ce groupe n'existe pas");
		
		if ($authService -> checkAuthAdminGroupe($groupe))
			$check_admin = true;
		if ($authService -> checkMemberGroupe($groupe)) {
			$check_member = true;
			$groupe -> updateLastVisit($user);
			$em = $this->getDoctrine()->getManager();
			$em->persist($groupe);
			$em->flush();
		}
	
        return $this->render('default/groupe/index.html.twig', array(
			'listPosts' => $listPosts,
			'groupe' => $groupe,
			'groupe_id' => $groupe -> getId(),
			'check_admin' => $check_admin,
			'check_member' => $check_member,
			'tag_name' => $tag_name,
			'title' => ($tag_name) ? $tag_name . ' - ' . $groupe -> getName() : $groupe -> getName(),
			'page' => $page,
			'nbPages' => $nbPages
		));
    }
}
