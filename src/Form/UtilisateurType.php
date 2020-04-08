<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Role;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('courriel', EmailType::class)
            ->add('telephone')
            ->add('dateNaissance', BirthdayType::class, [
                'format' => "dd-MM-yyyy"
            ])
            ->add('motDePasse', PasswordType::class)
            ->add('idImage', EntityType::class,[
                'class' => Image::class,
                'choice_label' => 'chemin'
            ])
            ->add('idRole', EntityType::class,[
                'class' => Role::class,
                'choice_label' => 'libelle'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
