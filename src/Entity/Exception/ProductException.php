<?php

declare(strict_types = 1);

namespace App\Entity\Exception;

class ProductException extends \DomainException
{
    public static function invalidName(string $name): self
    {
        return new self(sprintf('Name "%s" is not valid for a product', $name));
    }
}
