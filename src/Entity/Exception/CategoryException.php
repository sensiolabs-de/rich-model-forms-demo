<?php

declare(strict_types = 1);

namespace App\Entity\Exception;

use App\Entity\Category;

class CategoryException extends \DomainException
{
    public static function invalidName(string $name): self
    {
        return new self(sprintf('Name "%s" is not valid for a category', $name));
    }

    public static function hasNoParent(Category $category): self
    {
        return new self(sprintf('Category "%s" has no parent category', $category->getName()));
    }
}
