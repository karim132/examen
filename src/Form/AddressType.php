<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,[
                'label'=> 'Nom de l\'adresse',
            ])
            ->add('firstname',TextType::class,[
                'label'=> ' Votre nom',
            ])
            ->add('lastname',TextType::class,[
                'label'=> 'Votre prénom',
            ])
            ->add('company',TextType::class,[
                'label'=> 'Votre société',
                'required'=> false
            ])
            ->add('adress',TextType::class,[
                'label'=> 'Votre adresse',
            ])
            ->add('postal',TextType::class,[
                'label'=> 'Votre code postal',
            ])
            ->add('city',TextType::class,[
                'label'=> 'Votre ville',
            ])
            ->add('country',CountryType::class,[
                'label'=> 'Votre pays',
            ])
            ->add('phone',TelType::class,[
                'label'=> 'Votre téléphone',
            ])
            ->add('submit',SubmitType::class,[
                'label'=>'Ajouter mon adresse',
                'attr'=>[
                    'class' => 'btn btn-primary btn-lg mt-3 w-100'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
