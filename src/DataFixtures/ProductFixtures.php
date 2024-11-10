<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Product\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; $i++) {
            $product = Product::create(
              'product #' . $i,
              $i * 10,
                'url-for-product-' . $i,
                'img-url-for-product-' . $i,
            );

            $manager->persist($product);
        }

        $manager->flush();
    }
}