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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use AppBundle\Form\Type\DocumentType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PostType extends AbstractType
{
	
    const NAME = "add_post";
    
   // or alternativ you can set it via constructor (warning this is only a guess)

	/*public function __construct()
	{
		$this->name = $this -> getName();
		parent::__construct();
	}*/
  
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('title', TextType::class, array('required' => false))
            ->add('content', TextareaType::class, array('required' => true, 'attr' => array('class'=>'ckeditor')))
			->add('tag', TextType::class, array('required' => false, 'attr' => array('data-role' => 'tagsinput')))
			//->add('document', DocumentType::class)
			->add('document', DocumentType::class, array('required' => false))
			//->add('lastUpdate', HiddenType::class, array('required' => true, 'data' => new \DateTime()))
            ->add('save', SubmitType::class, array('label' => 'Envoyer la publication'))
        ;
    }
	
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Post',
		));
	}
	
    public function getName() {
		return self::NAME . '_' . uniqid();
    }
}
