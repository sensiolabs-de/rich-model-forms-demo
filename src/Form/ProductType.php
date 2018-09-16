<?php

declare(strict_types = 1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\Exception\PriceException;
use App\Entity\Exception\ProductException;
use App\Entity\Price;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType implements DataMapperInterface
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
            ->add('price', PriceType::class);

        $builder->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Product::class);
        $resolver->setDefault('empty_data', function (FormInterface $form) {
            try {
                return new Product(
                    $form->get('name')->getData(),
                    $form->get('category')->getData(),
                    $form->get('price')->getData()
                );
            } catch (PriceException $exception) {
                $form->addError(new FormError($exception->getMessage()));

                return null;
            } catch (ProductException $exception) {
                $form->get('name')->addError(new FormError($exception->getMessage()));

                return null;
            }
        });
    }

    /**
     * @param Product|null $data
     * @param FormInterface[]|\Traversable $forms
     */
    public function mapDataToForms($data, $forms): void
    {
        if (null === $data) {
            return;
        }

        $forms = iterator_to_array($forms);
        $forms['name']->setData($data->getName());
        $forms['category']->setData($data->getCategory());
        $forms['price']->setData($data->getPrice());
    }

    /**
     * @param FormInterface[]|\Traversable $forms
     * @param Product|null $data
     */
    public function mapFormsToData($forms, &$data): void
    {
        if (null === $data) {
            return;
        }

        $forms = iterator_to_array($forms);

        if ($data->getName() !== $forms['name']->getData()) {
            $data->rename($forms['name']->getData());
        }

        if ($data->getCategory() !== $forms['category']->getData()) {
            $data->moveToCategory($forms['category']->getData());
        }

        /** @var Price $price */
        $price = $forms['price']->getData();
        if (!$price->equals($data->getPrice())) {
            $data->costs($price);
        }
    }
}
