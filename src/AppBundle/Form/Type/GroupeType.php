<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GroupeType extends AbstractType
{
  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('name', TextType::class)
            ->add('description', TextareaType::class, array('required' => false))
			->add('statut', ChoiceType::class, array(
				'choices'  => array(
					'Accessible sur invitation' => 1,
					'Accessible à tous' => 2
				),'required' => true
				// *this line is important*
				))
			->add('authPost', ChoiceType::class, array(
				'choices'  => array(
					'Administrateurs uniquement' => 0,
					'Tous les membres du groupe' => 1
				),'required' => true
				// *this line is important*
				))
			->add('authInvitation', ChoiceType::class, array(
				'choices'  => array(
					'Administrateurs uniquement' => 0,
					'Tous les membres du groupe' => 1
				),'required' => true
				// *this line is important*
				))
				
			->add('code', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Enregistrer'))
			
        ;
    }
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Groupe',
		));
	}
}
