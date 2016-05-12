<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GroupeAddType extends GroupeType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		parent::buildForm($builder, $options);
		
		$builder
			-> remove ('code')
			-> remove ('authPost')
			-> remove ('authInvitation')
			-> remove ('statut')
		;
    }
	
	public function getName()
	{
		return 'appbundle_groupe_add';
	}
}
