<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Exception\ProductException;

class Product
{
    private $id = 0;
    private $name = '';
    private $category;
    private $price;

    public function __construct(string $name, Category $category, Price $price)
    {
        $this->validateName($name);

        $this->name = $name;
        $this->category = $category;
        $this->price = $price;
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

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function moveToCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function costs(Price $price): void
    {
        $this->price = $price;
    }

    private function validateName(string $name): void
    {
        if (strlen($name) < 3) {
            throw ProductException::invalidName($name);
        }
    }
}
