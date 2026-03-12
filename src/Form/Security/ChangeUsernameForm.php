<?php

declare(strict_types=1);

namespace App\Form\Security;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ChangeUsernameForm extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', null);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newUsername', TextType::class, [
                'label' => 'Neuer Benutzername',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 3, max: 50),
                ],
                'attr' => [
                    'autocomplete' => 'username',
                ],
            ])
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Aktuelles Passwort',
                'required' => true,
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'current-password',
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Benutzername ändern'])
        ;
    }
}
