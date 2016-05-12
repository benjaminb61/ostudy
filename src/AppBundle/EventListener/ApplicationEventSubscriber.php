<?php

// src/AppBundle/EventListener/ApplicationEventSubscriber.php
namespace AppBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Post;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Event;

class ApplicationEventSubscriber implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
			'preRemove',
        );
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->eventEdit($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
       $this->eventAdd($args);
    }
	
    public function preRemove(LifecycleEventArgs $args)
    {
        $this->eventRemove($args);
    }

    public function index(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // perhaps you only want to act on some "Product" entity
        if ($entity instanceof Product) {
            $entityManager = $args->getEntityManager();
            // ... do something with the Product
        }
    }
	
    public function eventRemove(LifecycleEventArgs $args)
    {   
		$entityManager = $args->getEntityManager();
		$entity = $args->getEntity();

        if ($entity instanceof Post) {
            $repository = $entityManager->getRepository('AppBundle:Event');
			$listEvents = $repository -> getEventByPost($entity -> getId());
			foreach ($listEvents as $event) {
				$entityManager -> remove($event);
			}
			$entityManager -> flush();
        }
        else if ($entity instanceof Comment) {
            $repository = $entityManager->getRepository('AppBundle:Event');
			$listEvents = $repository -> getEventByComment($entity -> getId());
			foreach ($listEvents as $event) {
				$entityManager -> remove($event);
			}
			$entityManager -> flush();
        }
        /*else if ($entity instanceof Groupe) {
            $repository = $entityManager->getRepository('AppBundle:Post');
			$listPosts = $repository -> getPostByGroupe($entity -> getSlug());
			foreach ($listPosts as $post) {
				$entityManager -> remove($post);
			}
			$entityManager -> flush();
        }*/
    }
	
    public function eventAdd(LifecycleEventArgs $args)
    {
		$entityManager = $args->getEntityManager();
        $entity = $args->getEntity();

        if ($entity instanceof Post) {
			$event = new Event();
			if ($entity -> getGroupes()) {
				foreach ($entity -> getGroupes() as $groupe) {
					$event ->addGroupe($groupe);
					$groupe -> updateLastUpdate();
					$entityManager -> persist($groupe);
				}
			}
			$event ->setPost($entity);	
			$event
				->setType('post-add')
				->setAuthor($entity -> getAuthor());
			
			$entityManager -> persist($event);
			$entityManager -> flush();
        }
        else if ($entity instanceof Comment) {
			$event = new Event();
			if ($entity -> getPost() -> getGroupes()) {
				foreach ($entity -> getPost() -> getGroupes() as $groupe) {
					$event ->addGroupe($groupe);
					$groupe -> updateLastUpdate();
					$entityManager -> persist($groupe);
				}
			}
			
			$event 
				->setComment($entity)
				->setPost($entity -> getPost());
			
			foreach ($entity -> getPost() -> getComments() as $com) {
				if ((is_array($event -> getUsers())) && (!in_array($com -> getAuthor(),$event -> getUsers())) && ($com -> getAuthor() != $entity -> getAuthor()))
					$event ->addUser($com -> getAuthor()); $added = true;
			}
			if (($entity -> getPost() -> getAuthor() != $entity -> getAuthor()) && !$added)
				$event ->addUser($entity -> getPost() -> getAuthor());
			
			$event
				->setType('comment-add')
				->setAuthor($entity -> getAuthor());
			
			$entityManager -> persist($event);
			$entityManager -> flush();
        }
    }
	
    public function eventEdit(LifecycleEventArgs $args)
    {
		$entityManager = $args->getEntityManager();
        $entity = $args->getEntity();

        if ($entity instanceof Post) {
			$event = new Event();
			if ($entity -> getGroupes()) {
				foreach ($entity -> getGroupes() as $groupe) {
					$event ->addGroupe($groupe);
					$groupe -> updateLastUpdate();
					$entityManager -> persist($groupe);
				}
			}
			$entityManager -> persist($entity);
			$event ->setPost($entity);
			
			$event
				->setType('post-edit')
				->setAuthor($entity -> getAuthor());
			
			$entityManager -> persist($event);
			$entityManager -> flush();
        }
    }
}