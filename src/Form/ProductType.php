<?php

declare(strict_types = 1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\Exception\PriceException;
use App\Entity\Exception\ProductException;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'read_property_path' => 'getName',
                'write_property_path' => 'rename',
                'expected_exception' => ProductException::class,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return $category->getName();
                },
                'read_property_path' => 'getCategory',
                'write_property_path' => 'moveToCategory',
            ])
            ->add('price', PriceType::class, [
                'read_property_path' => 'getPrice',
                'write_property_path' => 'costs',
                'expected_exception' => PriceException::class,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('factory', Product::class);
        $resolver->setDefault('expected_exception', [ProductException::class, PriceException::class]);
    }
}
