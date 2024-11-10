<?php

declare(strict_types=1);

namespace App\Infrastructure\Content\Writer;

use App\Application\Content\Exception\ContentWriteException;
use App\Application\Content\Writer\WriterInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class CSVWriter implements WriterInterface
{
    public const string SEPARATOR = ';';

    public function __construct(
        private LoggerInterface $logger,
        private string $storageFolder,
    ) {}

    /**
     * @throws ContentWriteException
     */
    public function write(array $headers, array $content): void
    {
        // mock filename
        $filePath = $this->storageFolder . 'products.csv';

        if (!is_dir($this->storageFolder) && !mkdir($this->storageFolder, 0777, true)) {
            throw new \Exception('Failed to create directory ' . $this->storageFolder);
        }

        try {
            if (!file_exists($filePath)) {
                touch($filePath);
                file_put_contents($filePath, implode(self::SEPARATOR, $headers) . PHP_EOL);
            }

            $file = fopen($filePath, 'ab');

            flock($file, LOCK_SH);
            fputcsv($file, $content, ";");
            flock($file, LOCK_UN);
            fclose($file);
        } catch (Throwable $e) {
            $this->logger->error('Error writing product into csv', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'headers' => $headers,
                'content' => $content,
            ]);

            throw new ContentWriteException();
        }
    }
}