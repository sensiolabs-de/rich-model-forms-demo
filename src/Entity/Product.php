<?php

declare(strict_types = 1);

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Product
{
    private $id;

    /**
     * @Assert\Length(min=3)
     */
    private $name;

    /**
     * @Assert\NotBlank
     */
    private $category;

    /**
     * @Assert\GreaterThan(0)
     */
    private $priceAmount;

    /**
     * @Assert\GreaterThanOrEqual(0)
     */
    private $priceTax;

    /**
     * @Assert\Currency
     */
    private $priceCurrency;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function getPriceAmount(): ?int
    {
        return $this->priceAmount;
    }

    public function setPriceAmount(?int $priceAmount): void
    {
        $this->priceAmount = $priceAmount;
    }

    public function getPriceTax(): ?int
    {
        return $this->priceTax;
    }

    public function setPriceTax(?int $priceTax): void
    {
        $this->priceTax = $priceTax;
    }

    public function getPriceCurrency(): ?string
    {
        return $this->priceCurrency;
    }

    public function setPriceCurrency(?string $priceCurrency): void
    {
        $this->priceCurrency = $priceCurrency;
    }
}
