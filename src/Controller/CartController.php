<?php

namespace App\Controller;

use DateTime;
use App\Entity\User;
use App\Entity\Orders;
use App\Entity\OrderItems;
use App\Repository\CartRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;


class CartController extends AbstractController
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var CartRepository
     */
    private $repository;
    /**
     * UserController constructor.
     * @param cartRepository $cartRepository
     */
    public function __construct(CartRepository $cartRepository)
    {
        $this->repository = $cartRepository;
    }

    /**
     * @Route("/api/cart/{productId}",methods={"GET"})
     */
    public function addProductToCart($productId, Request $request)
    {
        $product = $this->repository->findOneBy(["id" => $productId]);
        $products['total_price'] = 0;
        if (!is_null($request->getSession()->get('cart'))) {

            $products = $request->getSession()->get('cart');
            $products['products'][$productId] = $product->getName();
            $products['price'][$productId] = $product->getPrice();
            if (key_exists('product_quantity', $products)) {
                if (!key_exists($productId, $products['product_quantity'])) {
                    $products['product_quantity'][$productId] = 1;
                } else {
                    $products['product_quantity'][$productId]++;
                }
            } // else {
            //     $products['product_quantity'][$productId] = 1;
            // }
            // if (key_exists('product_total_price', $products)) {
            //     if (!key_exists($productId, $products['product_total_price'])) {
            //         $products['product_total_price'][$productId] = floatval($product->getPrice());
            //     } else {
            //         $products['product_total_price'][$productId] += floatval($product->getPrice());
            //     }
            // } else {
            //     $products['product_total_price'][$productId] = floatval($product->getPrice());
            // }
            // foreach (array_keys($products['products']) as $key) {
            //     $products['total_price'] += $products['product_total_price'][$key];
            // }

            $request->getSession()->set('cart', $products);
        } else {
            $products = $request->getSession()->get('cart');
            $products['products'][$productId] = $product->getName();
            $products['price'][$productId] = $product->getPrice();
            if (key_exists('product_quantity', $products)) {
                if (!key_exists($productId, $products['product_quantity'])) {
                    $products['product_quantity'][$productId] = 1;
                } else {
                    $products['product_quantity'][$productId]++;
                }
            } else {
                $products['product_quantity'][$productId] = 1;
            }
            if (key_exists('product_total_price', $products)) {
                if (!key_exists($productId, $products['product_total_price'])) {
                    $products['product_total_price'][$productId] = intval($product->getPrice());
                } else {
                    $products['product_total_price'][$productId] += intval($product->getPrice());
                }
            } else {
                $products['product_total_price'][$productId] = intval($product->getPrice());
            }
            $products['total_price'] = intval($product->getPrice());
            $request->getSession()->set('cart', $products);
        }
        return $this->json($products);
    }

    /**
     * @Route("/api/cart/{productId}", name="deletecart", methods={"DELETE"})
     */
    public function deleteProductFromCart($productId, Request $request): JsonResponse
    {

        $products = $request->getSession()->get('cart');

        unset($products['products'][$productId]);
        unset($products['price'][$productId]);
        unset($products['product_quantity'][$productId]);
        unset($products['product_total_price'][$productId]);
        // unset($products['total_price'][$productId]);
        $request->getSession()->set('cart', $products);
        dd($products);
        return $this->json(["message" => "le produit a bien été suprimmé du panier"]);
    }

    /**
     * @Route("/api/cart",methods={"GET"})
     */
    public function showCart(Request $request): Response
    {
        $products = $request->getSession()->get('cart');
        return $this->json($products);
    }

    /**
     * @Route("/api/cart/validate",methods={"POST"})
     */
    public function cartValidation(Request $request): Response
    {
        $db = $this->getDoctrine()->getManager();
        $repository = $db->getRepository(User::class);
        /** @var User $user */
        $user = $repository->findOneBy(array('id' => $this->getUser()->getId()));
        $finalprice = 0;
        $order = new Orders();
        $order->setTotalPrice(0);
        $order->setCreationDate(new DateTime());
        $order->setUser($user);
        $db->persist($order);

        $products = $request->getSession()->get('cart');
        $keys = array_keys($products['products']);
        // loop in products
        foreach ($keys as $key) {
            $product = $this->repository;
            $product = $product->find($key);
            $orderItems = new OrderItems();
            $orderItems->setQuantity($products['product_quantity'][$key]);
            $orderItems->setProduct($this->repository->find($key));
            $orderItems->setOrder($order);

            $db->persist($orderItems);
            $finalprice = +$products['price'][$key];
        }

        $order->setTotalPrice($finalprice);
        $db->persist($order);

        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers);


        return $this->json(
            $serializer->normalize($orderItems),
            Response::HTTP_ACCEPTED
        );
        // if ($db->flush()) {
        return new JsonResponse(array("message" => "Commande crée", "alldatas" => $orderItems));
        // } else {
        //     return new JsonResponse(array("message" => "Un problem est survenu pendant la creation de la commande"));
        // }
        $products = $request->getSession()->get('cart');
        return $this->json($products);
    }
}
