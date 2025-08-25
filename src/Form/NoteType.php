<?php

namespace App\Form;

use App\Entity\Candidatures;
use App\Entity\Note;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('valeur', ChoiceType::class, [
    'label' => 'Note',
    'choices' => [
        '⭐' => 1,
        '⭐⭐' => 2,
        '⭐⭐⭐' => 3,
        '⭐⭐⭐⭐' => 4,
        '⭐⭐⭐⭐⭐' => 5,
    ],
    'expanded' => false,
    'multiple' => false,
    'attr' => [
        'class' => 'star-rating'
    ],
])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
