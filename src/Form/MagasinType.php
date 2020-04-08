<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Magasin;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MagasinType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('telephone', TextType::class, [
                'label' => "Téléphone"
            ])
            ->add('courriel', EmailType::class)
            ->add('idImage', EntityType::class,[
                'class' => Image::class,
                'choice_label' => 'chemin',
                'label' => "Image"
            ])
            ->add('horaireOuverture', TextType::class, [
                'label' => "Horaire d'ouverture"
            ])
            ->add('longitude')
            ->add('latitude')
            ->add('id_adresse', AdresseType::class, [
                'label' => "Adresse"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Magasin::class,
        ]);
    }
}
