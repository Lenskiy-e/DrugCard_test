<?php

declare(strict_types=1);

namespace App\Tests\Content\Writer;

use App\Application\Content\Writer\WriterInterface;
use App\Infrastructure\Content\Writer\CSVWriter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WriterTest extends KernelTestCase
{
    private const string FILENAME = 'products.csv';
    private string $storagePath;

    protected function setUp(): void
    {
        $this->storagePath = self::getContainer()->getParameter('csv_storage_path');
        $this->cleanDir();
    }

    public function testDirectoryCreation(): void
    {
        /** @var WriterInterface $writer */
        $writer = self::getContainer()->get(WriterInterface::class);
        $writer->write([], []);

        self::assertDirectoryExists($this->storagePath);
        self::assertFileExists($this->storagePath . self::FILENAME);
    }

    public function testFileContent(): void
    {
        /** @var WriterInterface $writer */
        $writer = self::getContainer()->get(WriterInterface::class);
        $writer->write(['id', 'name'], ['some Id', 'Some name']);

        $f = fopen($this->storagePath . self::FILENAME, 'rb');

        $headers = fgetcsv($f, null, CSVWriter::SEPARATOR);
        $content = fgetcsv($f, null, CSVWriter::SEPARATOR);

        fclose($f);

        self::assertSame(['id', 'name'], $headers);
        self::assertSame(['some Id', 'Some name'], $content);
    }

    private function cleanDir(): void
    {
        if (is_dir($this->storagePath)) {
            unlink($this->storagePath . self::FILENAME);
            rmdir($this->storagePath);
        }
    }
}