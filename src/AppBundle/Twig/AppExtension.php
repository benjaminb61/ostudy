<?php
// src/AppBundle/Twig/AppExtension.php

namespace AppBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class AppExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('hashtag', array($this, 'replaceHashtag')),
        );
    }

    public function replaceHashtag($content,$path = null)
    {
		$newContent = preg_replace('/(>|\s)#((\w|\s|[&;]|(&#))*)#(<|\s|,|\.)/','$1<span class="label label-info tag"><a href="'.$path.'?tag_name=$2" title="$2">$2</a></span>$5',$content);
		//$content = preg_replace('/(^|\s)#\(((.*))\)/',' <a href="">$2</a> ',$this -> content);
		$newContent = preg_replace('/(>|\s)#((\w|[&;]|(&#))*)(<|\s|,|\.)/','$1<span class="label label-info tag"><a href="?tag_name=$2" title="$2">$2</a></span>$5',$newContent);
		return $newContent;
    }

    public function getName()
    {
        return 'app_extension';
    }
}