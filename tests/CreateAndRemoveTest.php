<?php

declare(strict_types = 1);

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Link;

class CreateAndRemoveTest extends WebTestCase
{
    /** @var Client */
    private static $client;
    /** @var SchemaTool */
    private static $schemaTool;

    public static function setUpBeforeClass(): void
    {
        self::$client = static::createClient();
        $entityManager = self::$client->getContainer()->get('test.'.EntityManagerInterface::class);

        self::$schemaTool = new SchemaTool($entityManager);
        self::$schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }

    public static function tearDownAfterClass(): void
    {
        self::$schemaTool->dropDatabase();
    }

    public function testCreateRootCategory(): void
    {
        $this->whenICreateCategory('food');
        $this->thenIShouldSeeASuccessMessage();
        $this->thenIShouldSeeCategory('food');
    }

    /**
     * @depends testCreateRootCategory
     */
    public function testCreateSubCategory(): void
    {
        $this->whenICreateCategory('vegetable', 'food');
        $this->thenIShouldSeeASuccessMessage();
        $this->thenIShouldSeeCategory('vegetable');
    }

    /**
     * @depends testCreateSubCategory
     */
    public function testCreateProduct(): void
    {
        $this->whenICreateProduct('apples', 'vegetable');
        $this->thenIShouldSeeASuccessMessage();
        $this->thenIshouldSeeProduct('apples');
    }

    /**
     * @depends testCreateProduct
     */
    public function testRemoveSubCategoryWhileHavingProduct(): void
    {
        $this->whenIRemoveCategory('vegetable');
        $this->thenIShouldSeeAnErrorMessage();
    }

    /**
     * @depends testRemoveSubCategoryWhileHavingProduct
     */
    public function testRemoveRootCategoryWhileHavingSubCategory(): void
    {
        $this->whenIRemoveCategory('food');
        $this->thenIShouldSeeAnErrorMessage();
    }

    /**
     * @depends testRemoveRootCategoryWhileHavingSubCategory
     */
    public function testRemoveProduct(): void
    {
        $this->whenIRemoveProduct('apples');
        $this->thenIShouldSeeASuccessMessage();
    }

    /**
     * @depends testRemoveProduct
     */
    public function testRemoveSubCategory(): void
    {
        $this->whenIRemoveCategory('vegetable');
        $this->thenIShouldSeeASuccessMessage();
    }

    /**
     * @depends testRemoveSubCategory
     */
    public function testRemoveRootCategory(): void
    {
        $this->whenIRemoveCategory('food');
        $this->thenIShouldSeeASuccessMessage();
    }

    private function whenICreateCategory(string $category, string $rootCategory = null): void
    {
        $crawler = self::$client->request('GET', '/category');

        $form = $crawler->selectButton('Submit')->form();
        $form['category[name]'] = $category;

        if (null !== $rootCategory) {
            $parentId = null;
            $crawler->filter('option')->each(function (Crawler $node) use ($rootCategory, &$parentId) {
                if ($rootCategory === $node->text()) {
                    $parentId = $node->attr('value');
                }
            });

            $form['category[parent]'] = $parentId;
        }

        self::$client->submit($form);
    }

    private function thenIShouldSeeASuccessMessage(): void
    {
        $crawler = self::$client->followRedirect();
        $this->assertContains('Successfully', $crawler->filter('.alert-success')->text());
    }

    private function thenIShouldSeeAnErrorMessage(): void
    {
        $crawler = self::$client->followRedirect();
        $this->assertContains('Cannot remove', $crawler->filter('.alert-danger')->text());
    }

    private function thenIShouldSeeCategory(string $category): void
    {
        $crawler = self::$client->request('GET', '/');
        $this->assertContains($category, $crawler->filter('#categories li')->last()->text());
    }

    private function whenICreateProduct(string $product, string $category): void
    {
        $crawler = self::$client->request('GET', '/product');

        $form = $crawler->selectButton('Submit')->form();
        $form['product[name]'] = $product;
        $form['product[priceAmount]'] = 1234;
        $form['product[priceTax]'] = 7;

        $categoryId = null;
        $crawler->filter('#product_category option')->each(function (Crawler $node) use ($category, &$categoryId) {
            if ($category === $node->text()) {
                $categoryId = $node->attr('value');
            }
        });

        $form['product[category]'] = $categoryId;

        self::$client->submit($form);
    }

    private function thenIshouldSeeProduct(string $product): void
    {
        $crawler = self::$client->request('GET', '/');
        $this->assertContains($product, $crawler->filter('#products li')->last()->text());
    }

    private function whenIRemoveCategory(string $category): void
    {
        $crawler = self::$client->request('GET', '/');

        $link = null;
        $crawler->filter('#categories li')->each(function (Crawler $node) use ($category, &$link) {
            $text = trim(str_replace(PHP_EOL, '', $node->filter('a')->first()->text()));
            if ($text === $category) {
                $link = $node->children()->last()->link();
            }
        });

        self::$client->click($link);
    }

    private function whenIRemoveProduct(string $product): void
    {
        $crawler = self::$client->request('GET', '/');

        $link = null;
        $crawler->filter('#products li')->each(function (Crawler $node) use ($product, &$link) {
            $text = trim(str_replace(PHP_EOL, '', $node->filter('a')->first()->text()));
            if ($text === $product) {
                $link = $node->children()->last()->link();
            }
        });

        self::$client->click($link);
    }
}
