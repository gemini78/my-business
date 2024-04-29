<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Common\Collections\Expr\Value;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom du produit',
            'attr' => [
                'placeholder' => 'Tapez le nom du produit'
            ]
        ])
        ->add('shortDescription', TextareaType::class, [
            'label' => 'Courte description',
            'attr' => [
                'placeholder' => 'Tapez une description courte du produit'
            ]
        ])
        ->add('price', MoneyType::class, [
            'label' => 'Prix du produit',
            'attr' => [
                'placeholder' => 'Tapez le prix du produit en €'
            ]
        ])

        ->add('mainPicture', UrlType::class, [
            'label' => 'Image du produit',
            'attr' => [
                'placeholder' => 'Tapez une URL d\'image'
            ]
        ])
        ->add('category', EntityType::class, [
            'label' => 'Catégorie du produit',
            'placeholder' => '-- Choisir une catégorie --',
            'class' => Category::class,
            'choice_label' => function (Category $category){
                return strtoupper($category->getName());
            }
        ])
        ;

        $builder->get('price')->addModelTransformer(new CallbackTransformer(
            function($value) { 
                if($value === null) {
                    return;
                }
                return $value / 100; 
            },
            function($value) { 
                if($value === null) {
                    return;
                }
                return $value * 100;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
