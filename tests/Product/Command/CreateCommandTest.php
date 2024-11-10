<?php

declare(strict_types=1);

namespace App\Tests\Product\Command;

use App\Application\Content\ContentDTO;
use App\Application\Product\Command\Create\CreateCommand;
use App\Application\Product\Command\Create\CreateHandler;
use App\Domain\Product\Product;
use App\Infrastructure\Content\Writer\CSVWriter;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateCommandTest extends KernelTestCase
{
    private const string FILENAME = 'products.csv';
    private ObjectRepository $repository;
    private string $storagePath;

    protected function setUp(): void
    {
        $manager = self::getContainer()->get('doctrine.orm.entity_manager');

        $this->repository = $manager->getRepository(Product::class);
        $manager->getConnection()->executeQuery('delete from product where 1');

        $this->storagePath = self::getContainer()->getParameter('csv_storage_path');
    }

    public function testProductWasCratedInDB(): void
    {
        $dto = new ContentDTO('Some name 123', 288.30, 'img-url', 'url');

        $commandHandler = self::getContainer()->get(CreateHandler::class);
        $commandHandler(new CreateCommand($dto));

        $product = $this->repository->findOneBy(['name' => 'Some name 123']);

        self::assertInstanceOf(Product::class, $product);

        $data = $product->getData();

        self::assertSame($dto->name, $data['name']);
        self::assertSame($dto->price, $data['price']);
        self::assertSame($dto->imgUrl, $data['imgUrl']);
        self::assertSame($dto->url, $data['url']);
    }

    public function testProductFileWasFilled(): void
    {
        if (is_dir($this->storagePath)) {
            unlink($this->storagePath . self::FILENAME);
            rmdir($this->storagePath);
        }

        $dtoOne = new ContentDTO('Some name 1', 111.11, 'img-url1', 'url1');
        $dtoTwo = new ContentDTO('Some name 2', 222.22, 'img-url2', 'url2');

        $commandHandler = self::getContainer()->get(CreateHandler::class);
        $commandHandler(new CreateCommand($dtoOne));
        $commandHandler(new CreateCommand($dtoTwo));

        $productOne = $this->repository->findOneBy(['name' => 'Some name 1']);
        $productTwo = $this->repository->findOneBy(['name' => 'Some name 2']);

        self::assertFileExists($this->storagePath . self::FILENAME);

        $f = fopen($this->storagePath . self::FILENAME, 'rb');
        $headers = fgetcsv($f, null, CSVWriter::SEPARATOR);
        $firstProduct = fgetcsv($f, null, CSVWriter::SEPARATOR);
        $secondProduct = fgetcsv($f, null, CSVWriter::SEPARATOR);
        $thirdProduct = fgetcsv($f, null, CSVWriter::SEPARATOR);
        fclose($f);

        self::assertEquals(array_keys($productOne->getData()), $headers);
        self::assertEquals(array_values($productOne->getData()), $firstProduct);
        self::assertEquals(array_values($productTwo->getData()), $secondProduct);
        self::assertEmpty($thirdProduct);
        self::assertEquals(2, $this->repository->count());
    }
}