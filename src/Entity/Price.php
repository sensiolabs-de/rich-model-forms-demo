<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Exception\PriceException;

class Price
{
    private $amount = 0;
    private $tax = 0;
    private $currency = '';

    /**
     * @throws PriceException
     */
    public function __construct(int $amount, int $tax, string $currency)
    {
        if ($amount <= 0) {
            throw PriceException::invalidAmount($amount);
        }

        if ($tax < 0) {
            throw PriceException::invalidTax($tax);
        }

        if (3 !== strlen($currency)) {
            throw PriceException::invalidCurrency($currency);
        }

        $this->amount = $amount;
        $this->tax = $tax;
        $this->currency = $currency;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getTax(): int
    {
        return $this->tax;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function equals(Price $price): bool
    {
        return $this->getAmount() === $price->getAmount()
            && $this->getTax() === $price->getTax()
            && $this->getCurrency() === $price->getCurrency();
    }
}
