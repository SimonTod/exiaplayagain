<?php

namespace ExiaplayagainBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class VotesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('limitedDate', DateTimeType::class, array(
                'data' => new \Datetime()
            ))
            ->add('game1', EntityType::class, array(
                'class' => 'ExiaplayagainBundle:Games',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => 'Choose game',
            ))
            ->add('game2', EntityType::class, array(
                'class' => 'ExiaplayagainBundle:Games',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => 'Choose game',
            ))
            ->add('game3', EntityType::class, array(
                'class' => 'ExiaplayagainBundle:Games',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => 'Choose game',
            ))
            ->add('game4', EntityType::class, array(
                'class' => 'ExiaplayagainBundle:Games',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => 'Choose game',
            ))
            ->add('save', SubmitType::class, array('label' => 'Submit'))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ExiaplayagainBundle\Entity\Votes',
        ));
    }

}
