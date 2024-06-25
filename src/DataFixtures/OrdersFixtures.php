<?php

namespace App\DataFixtures;

use App\Entity\Orders;
use App\Entity\product;
use App\Entity\OrderItems;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OrdersFixtures extends Fixture {
    public function load(ObjectManager $manager) {


        $user = new User();
        $user->setEmail("toto@coucou");
        $user->setRoles(["customer" => "1"]);
        $user->setPassword("titi");
        $user->setFirstname("arthur");
        $user->setLastname("pelisson");
        $user->setLogin("arthur");
        $manager->persist($user);

        $order1 = new Orders();
        $order1->setTotalPrice(45.05);
        $order1->setCreationDate(new \DateTime('06/04/2014'));
        $order1->setUser($user);
        $manager->persist($order1);

        $order = new Orders();
        $order->setTotalPrice(25.05);
        $order->setCreationDate(new \DateTime('06/04/2014'));
        $order->setUser($user);
        $manager->persist($order);

        $order2 = new Orders();
        $order2->setTotalPrice(25.05);
        $order2->setCreationDate(new \DateTime('06/04/2014'));
        $order2->setUser($user);
        $manager->persist($order2);
        
        $product = new Product();
        $product->setName("Item 3000");
        $product->setDescription("Best item in the shop !");
        $product->setPhoto("https://path/to/image.pn");
        $product->setPrice(13.37);
        $product->setCategory("carte mere");


        $product2 = new Product();
        $product2->setName("Item 3000");
        $product2->setDescription("Best item in the shop !");
        $product2->setPhoto("https://path/to/image.pn");
        $product2->setPrice(13.37);
        $product2->setCategory("carte mere");


        
        
        
        $OrderItems = new OrderItems();
        $OrderItems->setQuantity(1);
        $OrderItems->setProduct($product);
        // $ordertest = $OrderItems->getOrder($order1);
        $OrderItems->setOrder($order1);

        $OrderItems2 = new OrderItems();
        $OrderItems2->setQuantity(1);
        $OrderItems2->setProduct($product);
        // $ordertest = $OrderItems->getOrder($order1);
        $OrderItems2->setOrder($order1);
        
        
        
        $manager->persist(($OrderItems));
        $manager->persist(($OrderItems2));
        $manager->flush();
    }
}
