<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('name', TextType::class, [
            'label' => 'Nom du produit',
            'attr' => [
                'placeholder' => 'Tapez le nom du produit'
            ],
            'required' => false
        ])
        ->add('shortDescription', TextareaType::class, [
            'label' => 'Courte description',
            'attr' => [
                'placeholder' => 'Tapez une description courte du produit'
            ],
            'required' => false
        ])
        ->add('price', MoneyType::class, [
            'label' => 'Prix du produit',
            'attr' => [
                'placeholder' => 'Tapez le prix du produit en €'
            ],
            'divisor' => 100,
            'required' => false
        ])

        ->add('mainPicture', UrlType::class, [
            'label' => 'Image du produit',
            'attr' => [
                'placeholder' => 'Tapez une URL d\'image'
            ],
            'required' => false
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

    // Commented or delete because use divisor of moneytype
    //    $builder->get('price')->addModelTransformer(new CentimesTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
