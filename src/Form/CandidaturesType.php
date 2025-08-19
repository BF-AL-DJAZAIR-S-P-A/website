<?php

namespace App\Form;

use App\Entity\Candidatures;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Contracts\Translation\TranslatorInterface;

class CandidaturesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
   
        $builder
           

           ->add('poste', ChoiceType::class, [
    'choices' => [
        'form.poste.ingenieur_agronome' => 'Agronomy engineer',
        'form.poste.production_vegetale' => 'Plant production engineer',
        'form.poste.agroalimentaire' => 'Food industry engineer',
        'form.poste.electricite' => 'Electrical engineer',
        'form.poste.informatique' => 'IT engineer',
        'form.poste.irrigation' => 'Irrigation engineer',
        'form.poste.rh' => 'Human Resources Manager',
        'form.poste.logistique' => 'Logistics Manager',
        'form.poste.conducteur_engins' => 'Agricultural machinery driver',
    ],
    'choice_translation_domain' => 'forms',
    'placeholder' => '',
    'translation_domain' => 'forms',
    'expanded' => false,
    'multiple' => false,
    'required' => true,
    'label' => false,
])

          ->add('experience',ChoiceType::class, [
            'choices' => [
                "1an d'expérience" => "1an d'expérience",
                "2ans d'expérience" => "2ans d'expérience",
                "3ans d'expérience" => "3ans d'expérience",
                "4ans d'expérience" => "4ans d'expérience",
                "5ans d'expérience" => "5ans d'expérience",
                "6ans d'expérience" => "6ans d'expérience",
                "7ans d'expérience" => "7ans d'expérience",
                "8ans d'expérience" => "8ans d'expérience",
                "9ans d'expérience" => "9ans d'expérience",
                "10ans d'expérience" => "10ans d'expérience",
                "Plus de 10ans d'expérience" => "Plus de 10ans d'expérience",
                
            ],
            'placeholder' => '',
            'expanded' => false,
            'multiple' => false,
            'required' => true,
            'label' => false 
        ])
          
            ->add('nom',TextType::class,[
                'label'=> false,
                'required'=>true,
            ])

            ->add('prenom',TextType::class,[
                'label'=> false,
                'required'=>true,
            ])

             ->add('datenaissance',DateType::class,[
                'label' => false,
                'widget' => "single_text",
                'empty_data' => null,
            ])
           
             ->add('lettre',TextareaType::class,[
                'label'=> false,
                'required' => false,
                
            ])
          
            ->add('email',TextType::class,[
                'label'=> false,
                'required'=>true,
            ])
               ->add('tel',TextType::class,[
                'label'=> false,
                'required'=>true,
            ])
             ->add('ville',ChoiceType::class, [
            'choices' => [
            "01 Adrar" => "01 Adrar",
            "02 Chlef" => "02 Chlef",
            "03 Laghouat" => "03 Laghouat",
            "04 Oum El Bouaghi" => "04 Oum El Bouaghi",
            "05 Batna" => "05 Batna",
            "06 Béjaïa" => "06 Béjaïa",
            "07 Biskra" => "07 Biskra",
            "08 Béchar" => "08 Béchar",
            "09 Blida" => "09 Blida",
            "10 Bouira" => "10 Bouira",
            "11 Tamanrasset" => "11 Tamanrasset",
            "12 Tébessa" => "12 Tébessa",
            "13 Tlemcen" => "13 Tlemcen",
            "14 Tiaret" => "14 Tiaret",
            "15 Tizi Ouzou" => "15 Tizi Ouzou",
            "16 Alger" => "16 Alger",
            "17 Djelfa" => "17 Djelfa",
            "18 Jijel" => "18 Jijel",
            "19 Sétif" => "19 Sétif",
            "20 Saïda" => "20 Saïda",
            "21 Skikda" => "21 Skikda",
            "22 Sidi Bel Abbès" => "22 Sidi Bel Abbès",
            "23 Annaba" => "23 Annaba",
            "24 Guelma" => "24 Guelma",
            "25 Constantine" => "25 Constantine",
            "26 Médéa" => "26 Médéa",
            "27 Mostaganem" => "27 Mostaganem",
            "28 M'Sila" => "28 M'Sila",
            "29 Mascara" => "29 Mascara",
            "30 Ouargla" => "30 Ouargla",
            "31 Oran" => "31 Oran",
            "32 El Bayadh" => "32 El Bayadh",
            "33 Illizi" => "33 Illizi",
            "34 Bordj Bou Arreridj" => "34 Bordj Bou Arreridj",
            "35 Boumerdès" => "35 Boumerdès",
            "36 El Tarf" => "36 El Tarf",
            "37 Tindouf" => "37 Tindouf",
            "38 Tissemsilt" => "38 Tissemsilt",
            "39 El Oued" => "39 El Oued",
            "40 Khenchela" => "40 Khenchela",
            "41 Souk Ahras" => "41 Souk Ahras",
            "42 Tipaza" => "42 Tipaza",
            "43 Mila" => "43 Mila",
            "44 Aïn Defla" => "44 Aïn Defla",
            "45 Naâma" => "45 Naâma",
            "46 Aïn Témouchent" => "46 Aïn Témouchent",
            "47 Ghardaïa" => "47 Ghardaïa",
            "48 Relizane" => "48 Relizane",
            "49 Timimoun" => "49 Timimoun",
            "50 Bordj Badji Mokhtar" => "50 Bordj Badji Mokhtar",
            "51 Ouled Djellal" => "51 Ouled Djellal",
            "52 Béni Abbès" => "52 Béni Abbès",
            "53 In Salah" => "53 In Salah",
            "54 In Guezzam" => "54 In Guezzam",
            "55 Touggourt" => "55 Touggourt",
            "56 Djanet" => "56 Djanet",
            "57 El M'Ghair" => "57 El M'Ghair",
            "58 El Meniaa" => "58 El Meniaa"
                
            ],
            'placeholder' => '',
            'expanded' => false,
            'multiple' => false,
            'required' => true,
            'label' => false 
        ])
           ->add('cv', FileType::class, [
            'label' => false,
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new \Symfony\Component\Validator\Constraints\File([
                    'maxSize' => '50M',
                    'mimeTypes' => [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    ],
                    'mimeTypesMessage' => 'Seuls les fichiers PDF, DOC ou DOCX sont autorisés.',
                ])
            ],
        ])
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidatures::class,
        ]);
    }
}
