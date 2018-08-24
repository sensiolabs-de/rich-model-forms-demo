<?php

declare(strict_types = 1);

namespace App\Form;

use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('parent', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function (Category $category) {
                    return $category->getName();
                },
                'placeholder' => 'no parent',
                'query_builder' => $this->parentLoader($options['data'] ?? null),
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Category::class);
    }

    private function parentLoader(?Category $category): ?\Closure
    {
        if (null === $category) {
            return null;
        }

        return function (EntityRepository $repository) use ($category) {
            $queryBuilder = $repository->createQueryBuilder('category');

            return $queryBuilder
                ->where($queryBuilder->expr()->neq('category.id', ':id'))
                ->setParameter('id', $category->getId())
                ->orderBy('category.name', 'ASC');
        };
    }
}
