<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'disabled'=>true,
                'label'=> 'Mon adresse mail'
            ])
            ->add('old_password',PasswordType::class,[
                'label'=> 'Mon mot de passe actuel',
                'mapped'=>false,
            ])
            ->add('new_password',RepeatedType::class,[
                'type'=>PasswordType::class,
                'mapped'=>false,
                'invalid_message'=> 'Le mot de passe et la confirmation doivent être identiques',
                'required'=> true,
                 'label'=> 'Mon nouveau mot de passe',
                'first_options'=>['label'=>'Mon nouveau mot de passe'],
                'second_options'=>['label'=>'Confirmer nouveau mot de passe']
            ])
            ->add('firstname',TextType::class,[
                'disabled'=>true,
                'label'=> 'Mon prénom'
            ])
            ->add('lastname',TextType::class,[
                'disabled'=>true,
                'label'=> 'Mon nom'
            ])
            ->add('submit',SubmitType::class,[
                'label'=> 'Mettre à jour',
                'attr'=> [
                    'class'=>'btn btn-primary btn-lg mt-3 w-100'
                ]
            ])
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
