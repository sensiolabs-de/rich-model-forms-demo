<?php

declare(strict_types = 1);

namespace App\Form\Dto;

use App\Entity\Price;
use App\Entity\Product as ProductEntity;
use Symfony\Component\Validator\Constraints as Assert;

final class Product
{
    public $id;

    /**
     * @Assert\Length(min=3)
     */
    public $name;

    /**
     * @Assert\NotBlank
     */
    public $category;

    /**
     * @Assert\GreaterThan(0)
     */
    public $priceAmount;

    /**
     * @Assert\GreaterThanOrEqual(0)
     */
    public $priceTax;

    /**
     * @Assert\Currency
     */
    public $priceCurrency;

    public static function fromEntity(ProductEntity $product = null): self
    {
        $self = new self();

        if (null === $product) {
            return $self;
        }

        $self->id = $product->getId();
        $self->name = $product->getName();
        $self->category = $product->getCategory();
        $self->priceAmount = $product->getPrice()->getAmount();
        $self->priceTax = $product->getPrice()->getTax();
        $self->priceCurrency = $product->getPrice()->getCurrency();

        return $self;
    }

    public function toEntity(ProductEntity $product = null): ProductEntity
    {
        $price = new Price((int) $this->priceAmount, $this->priceTax, $this->priceCurrency);

        if (null === $product) {
            return new ProductEntity($this->name, $this->category, $price);
        }

        if ($product->getName() !== $this->name) {
            $product->rename($this->name);
        }

        if ($product->getCategory() !== $this->category) {
            $product->moveToCategory($this->category);
        }

        if (!$price->equals($product->getPrice())) {
            $product->costs($price);
        }

        return $product;
    }
}
