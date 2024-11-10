<?php

declare(strict_types=1);

namespace App\Tests\Product\API;

use App\DataFixtures\ProductFixtures;
use App\Domain\Product\Product;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GetCollectionTest extends WebTestCase
{
    private ObjectRepository $repository;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $manager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->repository = $manager->getRepository(Product::class);

        $loader = new Loader();
        $loader->addFixture(new ProductFixtures());

        $purger = new ORMPurger($manager);
        $executor = new ORMExecutor($manager, $purger);
        $executor->execute($loader->getFixtures());
    }

    public function testFullList(): void
    {
        $products = $this->repository->findBy([], ['id' => 'ASC']);
        $this->client->request('GET', '/api/list');
        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(count($products), $response['total_count']);
        self::assertCount(count($products), $response['products']);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        for ($i = 0; $i < $response['total_count']; $i++) {
            self::assertSame($products[$i]->getData(), $response['products'][$i]);
        }
    }

    public function testPagination(): void
    {
        $this->client->request('GET', '/api/list?page=3&limit=1');

        $product = $this->repository->findOneBy(['name' => 'product #3'], ['id' => 'ASC']);
        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(10, $response['total_count']);
        self::assertCount(1, $response['products']);
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertEquals($product->getData() ,$response['products'][0]);
    }

    public function testBadPage(): void
    {
        $this->client->request('GET', '/api/list?page=-3&limit=1');
        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertSame(['error' => 'Parameter page must be greater or equal 0'], $response);
    }

    public function testBadLimit(): void
    {
        $this->client->request('GET', '/api/list?page=3&limit=-1');
        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertSame(['error' => 'Parameter limit must be greater or equal 1'], $response);
    }
}