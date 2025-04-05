<?php

namespace App\Form;

use App\Entity\Appels;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class AppelsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre',TextType::class,[
                'label' =>  false

            ])
            ->add('texte', TextareaType::class, [
                'label' => false,
                'required' => true,
                'attr' => ['rows' => 5], // optionnel pour définir la hauteur
            ])
            ->add('image', FileType::class, [
                'label' => false,
                'mapped' => false, // très important si le champ n’est pas directement dans l’entité
                'required' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Format d’image non valide.',
                    ])
                ],
            ])
            ->add('date', DateType::class, [
                'label' => false,
                'widget' => 'single_text', // pour un input HTML5 <input type="date">
                'data' => new \DateTime(),
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appels::class,
        ]);
    }
}
