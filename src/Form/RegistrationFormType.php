<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function PHPSTORM_META\type;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname',TextType::class,[
                'label'=> 'Nom'
            ])
            ->add('firstname',TextType::class,[
                'label'=> 'Prénom'
            ])
          
            ->add('email',EmailType::class,[
                'label'=> 'Email'
            ])
            ->add('password',RepeatedType::class,[
                'type'=>PasswordType::class,
                'invalid_message'=> 'Le mot de passe et la confirmation doivent être identiques',
                'required'=> true,
                 'label'=> 'Mot de passe',
                'first_options'=>['label'=>'Mot de passe'],
                'second_options'=>['label'=>'Confirmer mot de passe']
            ])

            ->add('submit',SubmitType::class,[
                'label'=> 'S\'inscrire',
                'attr'=>[
                    'class'=>'btn btn-success btn-lg mt-3 w-100'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
