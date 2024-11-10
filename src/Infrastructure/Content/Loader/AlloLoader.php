<?php

declare(strict_types=1);

namespace App\Infrastructure\Content\Loader;

use App\Application\Content\ContentDTO;

class AlloLoader extends AbstractLoader
{
    protected function parseDocument(\DOMXPath $xPath): array
    {
        $result = [];

        $query = "//div[@class='product-card']";

        foreach ($xPath->query($query) as $product) {
            if($product->childElementCount === 0) {
                continue;
            }

            $name = $xPath->query(".//a[@class='product-card__title']", $product)->item(0);
            $price = $xPath->query(".//div[contains(@class, 'v-pb__cur')]", $product)->item(0);
            $imgLink = $xPath->query(".//img[@class='gallery__img']", $product)->item(0);
            $link = $xPath->query(".//a[@class='image-carousel']", $product)->item(0);

            $result[] = new ContentDTO(
                name: $name?->textContent ? $this->getName($name->textContent) : 'No name',
                price: $price?->textContent ? $this->getPrice($price->textContent) : 0,
                imgUrl: $imgLink?->hasAttribute('src') ? $imgLink->getAttribute('src') : 'No image',
                url: $link?->hasAttribute('href') ? $link->getAttribute('href') : 'No link',
            );
        }

        return $result;
    }
}
