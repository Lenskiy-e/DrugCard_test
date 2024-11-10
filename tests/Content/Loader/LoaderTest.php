<?php

declare(strict_types=1);

namespace App\Tests\Content\Loader;

use App\Application\Content\ContentDTO;
use App\Application\Content\Loader\LoaderFactoryInterface;
use App\Application\Content\Loader\LoaderType;
use App\Infrastructure\Content\Loader\AlloLoader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LoaderTest extends KernelTestCase
{
    public function testLoaderFactory(): void
    {
        /** @var LoaderFactoryInterface $factory */
        $factory = self::getContainer()->get(LoaderFactoryInterface::class);
        $loader = $factory->get(LoaderType::ALLO);

        self::assertInstanceOf(AlloLoader::class, $loader);
    }

    public function testAlloLoaderWithOneProduct(): void
    {
        $html = <<<HTML
            <html>
                <body>
                    <div class="product-card">
                        <a class="product-card__title">Some Product</a>
                        <div class="v-pb__cur">23 550₴</div>
                        <img class="gallery__img" src="image.jpg">
                        <a class="image-carousel" href="/product/some.img"></a>
                    </div>
                </body>
            </html>
        HTML;

        $dom = new \DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_use_internal_errors(false);
        $xPath = new \DOMXPath($dom);

        $loader = $this->getMockBuilder(AlloLoader::class)->onlyMethods(['getXPath'])->getMock();
        $loader->method('getXPath')->willReturn($xPath);

        $result = $loader->load('fake url');

        $this->assertCount(1, $result);
        $this->assertInstanceOf(ContentDTO::class, $result[0]);

        $dto = $result[0];
        $this->assertSame('Some Product', $dto->name);
        $this->assertSame(23550.0, $dto->price);
        $this->assertSame('image.jpg', $dto->imgUrl);
        $this->assertSame('/product/some.img', $dto->url);
    }

    public function testAlloLoaderWithoutProducts(): void
    {
        $html = <<<HTML
            <html>
                <body>
                    <div class="product-card">
                    </div>
                </body>
            </html>
        HTML;

        $dom = new \DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_use_internal_errors(false);
        $xPath = new \DOMXPath($dom);

        $loader = $this->getMockBuilder(AlloLoader::class)->onlyMethods(['getXPath'])->getMock();
        $loader->method('getXPath')->willReturn($xPath);

        $result = $loader->load('fake url');

        $this->assertCount(0, $result);
    }

    public function testAlloLoaderWithoutProductNameAndPrice(): void
    {
        $html = <<<HTML
            <html>
                <body>
                    <div class="product-card">
                        <a class="product-card__title"></a>
                        <div class="v-pb__cur"></div>
                        <img class="gallery__img" src="image.jpg">
                        <a class="image-carousel" href="/product/some.img"></a>
                    </div>
                </body>
            </html>
        HTML;

        $dom = new \DOMDocument();

        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_use_internal_errors(false);
        $xPath = new \DOMXPath($dom);

        $loader = $this->getMockBuilder(AlloLoader::class)->onlyMethods(['getXPath'])->getMock();
        $loader->method('getXPath')->willReturn($xPath);

        $result = $loader->load('fake url');

        $this->assertCount(1, $result);
        $this->assertInstanceOf(ContentDTO::class, $result[0]);

        $dto = $result[0];
        $this->assertSame('No name', $dto->name);
        $this->assertSame(0.0, $dto->price);
        $this->assertSame('image.jpg', $dto->imgUrl);
        $this->assertSame('/product/some.img', $dto->url);
    }
}