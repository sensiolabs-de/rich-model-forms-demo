<?php

declare(strict_types = 1);

namespace App\Form;

use App\Entity\Category;
use App\Form\Dto\Product as ProductDto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return $category->getName();
                },
            ])
            ->add('priceAmount', MoneyType::class, [
                'divisor' => 100,
            ])
            ->add('priceTax', PercentType::class, [
                'type' => 'integer',
            ])
            ->add('priceCurrency', CurrencyType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', ProductDto::class);
    }
}
