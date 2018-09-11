<?php

declare(strict_types = 1);

namespace App\Form\Dto;

use App\Entity\Category as CategoryEntity;
use Symfony\Component\Validator\Constraints as Assert;

final class Category
{
    public $id;

    /**
     * @Assert\Length(min=3)
     */
    public $name;

    public $parent;

    public static function fromEntity(CategoryEntity $category = null): self
    {
        $self = new self();

        if (null === $category) {
            return $self;
        }

        $self->id = $category->getId();
        $self->name = $category->getName();
        if ($category->hasParent()) {
            $self->parent = $category->getParent();
        }

        return $self;
    }

    public function toEntity(CategoryEntity $category = null): CategoryEntity
    {
        if (null === $category) {
            return new CategoryEntity($this->name, $this->parent);
        }

        if ($category->getName() !== $this->name) {
            $category->rename($this->name);
        }

        if ($category->hasParent() && null === $this->parent) {
            $category->removeParent();
        } elseif (null !== $this->parent) {
            $category->moveTo($this->parent);
        }

        return $category;
    }
}
