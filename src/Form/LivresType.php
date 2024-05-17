<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Livres;
use App\Entity\Panier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('isbn')
            ->add('slug')
            ->add('image')
            ->add('resume')
            ->add('editeur')
            ->add('editedAt')
            ->add('prix')
            ->add('Qte')
            ->add('Auteur')
            ->add('categorie', EntityType::class, [
                'class' => Categories::class,
'choice_label' => 'id',
            ])
            ->add('panier', EntityType::class, [
                'class' => Panier::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livres::class,
        ]);
    }
}
