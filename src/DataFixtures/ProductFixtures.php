<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture {
    public function load(ObjectManager $manager) {

        for ($i = 0; $i < 10; $i++) {

            $product = new Product();
            $product->setName("Item 3000");
            $product->setDescription("Best item in the shop !");
            $product->setPhoto("https://path/to/image.pn");
            $product->setPrice(13.37);
            $product->setCategory("carte mere");
    
            $manager->persist($product);
        }
        $manager->flush();
    }
}
