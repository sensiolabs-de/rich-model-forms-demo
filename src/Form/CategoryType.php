<?php

declare(strict_types = 1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\Exception\CategoryException;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType implements DataMapperInterface
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

        $builder->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Category::class);
        $resolver->setDefault('empty_data', function (FormInterface $form) {
            try {
                return new Category(
                    $form->get('name')->getData(),
                    $form->get('parent')->getData()
                );
            } catch (CategoryException $exception) {
                $form->get('name')->addError(new FormError($exception->getMessage()));

                return null;
            }
        });
    }

    /**
     * @param Category|null $data
     * @param FormInterface[]|\Traversable $forms
     */
    public function mapDataToForms($data, $forms): void
    {
        if (null === $data) {
            return;
        }

        $forms = iterator_to_array($forms);
        $forms['name']->setData($data->getName());

        if ($data->hasParent()) {
            $forms['parent']->setData($data->getParent());
        }
    }

    /**
     * @param FormInterface[]|\Traversable $forms
     * @param Category|null $data
     */
    public function mapFormsToData($forms, &$data): void
    {
        if (null === $data) {
            return;
        }

        $forms = iterator_to_array($forms);

        if ($data->getName() !== $forms['name']->getData()) {
            try {
                $data->rename($forms['name']->getData());
            } catch (CategoryException $exception) {
                $forms['name']->addError(new FormError($exception->getMessage()));
            }
        }

        if ($data->hasParent() && null === $forms['parent']->getData()) {
            $data->removeParent();
        } elseif (null !== $forms['parent']->getData()) {
            $data->moveTo($forms['parent']->getData());
        }
    }

    private function parentLoader(?Category $category): ?\Closure
    {
        if (null === $category || null === $category->getId()) {
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
