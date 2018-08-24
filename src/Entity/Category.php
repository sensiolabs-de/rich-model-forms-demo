<?php

declare(strict_types = 1);

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Category
{
    private $id;

    /**
     * @Assert\Length(min=3)
     */
    private $name;
    private $parent;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getParent(): ?Category
    {
        return $this->parent;
    }

    public function setParent(?Category $parent): void
    {
        $this->parent = $parent;
    }

    public function getParentNames(): array
    {
        $names = [];
        $category = $this;
        $i = 0;

        while ($parent = $category->getParent()) {
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
}
