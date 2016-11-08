<?php

namespace ExiaplayagainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GamesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('type', TextType::class)
            ->add('availability', ChoiceType::class, array(
                'choices' => array(
                    'Pay' => 'Pay',
                    'Free To Play' => 'Free To Play',
                    'Crack Dispo' => 'Crack Dispo'
                ),
                'placeholder' => 'Availability'
            ))
            ->add('price', MoneyType::class, array('required' => false))
            ->add('info', TextareaType::class, array('required' => false))
            ->add('url', UrlType::class, array('required' => false))
            ->add('image', FileType::class, array('required' => false, 'data_class' => null))
            ->add('save', SubmitType::class, array('label' => 'Submit'))
        ;

        //http://symfony.com/doc/2.8/controller/upload_file.html
        //how to upload file and use FileType
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ExiaplayagainBundle\Entity\Games'
        ));
    }
}
