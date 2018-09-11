<?php

declare(strict_types = 1);

namespace App\Entity\Exception;

class PriceException extends \DomainException
{
    public static function invalidAmount(int $value): self
    {
        return new self(sprintf('Value "%s" is not a valid price value', $value));
    }

    public static function invalidTax(int $tax): self
    {
        return new self(sprintf('Tax rate "%s" is not valid for a price', $tax));
    }

    public static function invalidCurrency(string $currency): self
    {
        return new self(sprintf('The given currency "%s" is invalid.', $currency));
    }
}
