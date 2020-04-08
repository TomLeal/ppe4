<?php

namespace App\Form;

use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Edition;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextType::class)
            ->add('description', TextType::class)
            ->add('prixht', TextType::class)
            ->add('stock', TextType::class)
            ->add('idCategorie', EntityType::class,[
                'class' => Categorie::class,
                'choice_label' => "libelle"
            ])
            ->add('idEdition', EntityType::class,[
                'class' => Edition::class,
                'choice_label' => 'libelle'
            ])
            ->add('idAuteur', EntityType::class,[
                'class' => Auteur::class,
                'choice_label' => "nom"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
