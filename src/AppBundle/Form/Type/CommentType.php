<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CommentType extends AbstractType
{
	
    //const NAME = "add_comment";
    
   // or alternativ you can set it via constructor (warning this is only a guess)

  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('author', TextType::class)
            ->add('content', TextType::class)
			//->add('redirectUrl', HiddenType::class)
            //->add('date',      'date')
            //->add('published', 'checkbox', array('required' => false))
            //->add('article',   'text')
			->add('Envoyer', SubmitType::class)
        ;
    }
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Comment',
		));
	}
}
