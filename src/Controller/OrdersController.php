<?php
// src/Controller/OrdersController.php
namespace App\Controller;

use App\Entity\Orders;
use App\Entity\ItemsOrder;
use App\Entity\OrderItems;
use App\Repository\ItemsOrderRepository;
use App\Repository\OrderItemsRepository;
use App\Repository\ProductRepository;
use App\Repository\OrdersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ObjectManager;


class OrdersController extends AbstractController {
    /**
    * @Route("/api/orders/")
    */
    public function getAllOrdersOfUser(OrderItemsRepository $orderItemsRepository): Response {
      $orders =  $this->getDoctrine()
      ->getRepository(Orders::class)
      ->findBy(array("user" => 2));

      $allOrders = array();

      if (empty($orders)) {
        return new JsonResponse(array("error"=> ["code" => 201,"message" => "Data not fund"]));
      }

      for ($l = 0; $l < count($orders); $l++) {
        $orderItems = $orderItemsRepository->findBy(array("order" => $orders[$l]->getId()));
        $products = array();
        for ($i = 0; $i < count($orderItems); $i++) {
          array_push($products, $orderItems[$i]->getProduct());
        }
        
        $ordersAndProducts = [
          "orders" => $orders[$l],
          "products" => $products
        ];

        array_push($allOrders, $ordersAndProducts);
      }
      
        return $this->json($allOrders);
    }

     /**
    * @Route("/api/order/{idOrder}", methods = {"GET"})
    */
    public function getOrderById(Request $request, OrderItemsRepository $orderItemsRepository): Response {
      $order = $this->getDoctrine()
          ->getRepository(Orders::class)
          ->find($request->get('idOrder'));
          
      if (empty($order)) {
          return new JsonResponse(array("error"=> ["code" => 201,"message" => "Data not fund"]));
      }

      $orderItems = $orderItemsRepository->findBy(array("order" => $order->getId()));
      $products = array();

      for ($i = 0; $i < count($orderItems); $i++) {
        array_push($products, $orderItems[$i]->getProduct());
      }

      $orderAndProducts = [
        "orders" => $order,
        "products" => $products
      ];
      return $this->json($orderAndProducts);
    }

      /**
    * @Route("/api/order/", methods={"GET"})
    */
    public function createOrder(Request $request, ObjectManager $manager, ProductRepository $productRepository) {
      

        // $finalprice = 0;
        // $order = new Orders();
        // $order->setTotalPrice(0);
        // $order->setCreationDate(new \DateTime('06/04/2014'));
        // $manager->persist($order);

        // $products = $request->request;
        
        // // loop in products
        // for ($i = 0; $i < count($products); $i++) {
        //     $orderItems = new OrderItems();
        //     $orderItems->setQuantity($products[$i]->get("quantity"));
        //     $orderItems->setProduct($productRepository->findOneBy(array('id', $products[$i]->get('id'))));
        //     $orderItems->setOrder($order);
            
        //     $manager->persist($orderItems);
        //     $finalprice =+ $products[$i]->get('price');
        // }

        // $order->setTotalPrice($finalprice);
        // $manager->persist($order);
       
        // if($manager->flush()) {
        //     return new JsonResponse(array("message" => "Commande crée"));
        // } else {
        //     return new JsonResponse(array("message" => "Un problem est survenu pendant la creation de la commande"));
        // }
    }
}