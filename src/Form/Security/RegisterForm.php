<?php

declare(strict_types=1);

namespace App\Form\Security;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterForm extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', User::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'username', type: TextType::class, options: [
                'label' => 'Benutzername',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Wähle einen Benutzernamen (mind. 3 Zeichen)',
                    'autocomplete' => 'username',
                ],
            ])
            ->add(child: 'email', type: EmailType::class, options: [
                'label' => 'Emailaddresse bestätigen',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Emailaddresse bestätigen',
                    'autocomplete' => 'email',
                ],
            ])
            ->add(child: 'plainpassword', type: RepeatedType::class, options: [
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'Passwörter müssen übereinstimmen',
                'first_options' => [
                    'label' => 'Passwort',
                    'hash_property_path' => 'password',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                    'constraints' => [new Assert\PasswordStrength()],
                ],
                'second_options' => [
                    'label' => 'Passwort bestätigen',
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Registrieren'])
        ;
    }
}
