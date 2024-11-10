<?php

declare(strict_types=1);

namespace App\Infrastructure\Content\Loader;

use App\Application\Content\Loader\LoaderInterface;

abstract class AbstractLoader implements LoaderInterface
{
    public function load(string $url): array
    {
        return $this->parseDocument(
            $this->getXPath($url)
        );
    }

    abstract protected function parseDocument(\DOMXPath $xPath): array;

    protected function getXPath(string $url): \DOMXPath
    {
        $html = file_get_contents($url);
        $dom = new \DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_use_internal_errors(false);

        return new \DOMXPath($dom);
    }

    protected function getName(string $source): string
    {
        return mb_convert_encoding(trim($source), "ISO-8859-1", "UTF-8");
    }

    protected function getPrice(string $textPrice): float
    {
        return (float)preg_replace('/\D/', '',  trim($textPrice));
    }
}