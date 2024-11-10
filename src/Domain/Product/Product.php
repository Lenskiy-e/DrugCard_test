<?php

declare(strict_types=1);

namespace App\Domain\Product;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity]
class Product
{
    #[
        Column,
        Id,
        GeneratedValue(strategy: 'AUTO')
    ]
    public ?int $id;

    #[Column(type: Types::STRING, nullable: false)]
    public string $name;

    #[Column(type: Types::BIGINT, nullable: false)]
    public int $price;

    #[Column(type: Types::TEXT, nullable: false)]
    public string $imgUrl;

    #[Column(type: Types::TEXT, nullable: false)]
    public string $url;

    public function __construct(
        string $name, int $price,
        string $url, string $imgUrl,
    ) {
        $this->name = $name;
        $this->price = $price;
        $this->imgUrl = $imgUrl;
        $this->url = $url;
    }

    public static function create(
        string $name, float $price,
        string $url, string $imgUrl
    ): self
    {
        $price *= 100;

        return new self($name, (int)$price, $url, $imgUrl);
    }

    public function getData(): array
    {
        return [
            'name' => $this->name,
            'price' => $this->price / 100,
            'url' => $this->url,
            'imgUrl' => $this->imgUrl,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }
}