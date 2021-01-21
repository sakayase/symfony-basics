<?php

namespace App\Form;

use App\Entity\SchoolYear;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolYearType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('dateStart')
            ->add('dateEnd')
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => function($user) {
                    return "{$user->getFirstName()} {$user->getLastName()} ({$user->getId()})";
                },
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SchoolYear::class,
            'attr' => [                      //rajout des attributs pour le <form>
                'novalidate' => 'novalidate', //desactive la validation html5 coté client
            ]
        ]);
    }
}
