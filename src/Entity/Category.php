<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Exception\CategoryException;

class Category
{
    private $id = 0;
    private $name = '';
    private $parent;

    /**
     * @throws CategoryException if name is not valid
     */
    public function __construct(string $name, Category $parent = null)
    {
        $this->validateName($name);

        $this->name = $name;
        $this->parent = $parent;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $name): void
    {
        $this->validateName($name);

        $this->name = $name;
    }

    public function hasParent(): bool
    {
        return $this->parent !== null;
    }

    /**
     * @throws CategoryException if category has no parent
     */
    public function getParent(): Category
    {
        if (!$this->hasParent()) {
            throw CategoryException::hasNoParent($this);
        }

        return $this->parent;
    }

    public function moveTo(Category $parent): void
    {
        $this->parent = $parent;
    }

    public function removeParent(): void
    {
        $this->parent = null;
    }

    public function getParentNames(): array
    {
        $names = [];
        $category = $this;
        $i = 0;

        while ($category->hasParent()) {
            $parent = $category->getParent();
            if (3 === $i) {
                $names[] = '...';
                break;
            }

            array_unshift($names, $parent->getName());

            $category = $parent;
            ++$i;
        }

        return $names;
    }

    private function validateName(string $name): void
    {
        if (strlen($name) < 3) {
            throw CategoryException::invalidName($name);
        }
    }
}
