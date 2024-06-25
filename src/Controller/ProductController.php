<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Config\Definition\Exception\Exception;


class ProductController extends AbstractController
{

    /**
     * @var ProductRepository
     */
    private $repository;
    /**
     * UserController constructor.
     * @param ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->repository = $productRepository;
    }

    /**
     * @Route("/api/products",methods={"GET"})
     */
    public function getAllProducts(ProductRepository $productRepository): Response
    {


        $products = $productRepository->findAll();

        if (!empty($products)) {
            return $this->json($products);
        } else {
            return new JsonResponse(array('error' => "aucun article"));
        }
    }

    /**
     * @Route("/api/product/{productId}",methods={"GET"})
     */
    public function getProductById($productId, Request $request): Response
    {

        $product = $this->repository->findOneBy(["id" => $productId]);
        if (!empty($product)) {
            return $this->json($product);
        } else {
            return new JsonResponse(array('error' => "aucun article n'a été trouvé"));
        }
    }

    /**
     * @Route("/api/product/{productId}", name="updateProduct", methods={"PUT"})
     */
    public function update($productId, Request $request): JsonResponse
    {
        $data = $request->request->all();
        $products = $this->repository->findOneBy(["id" => $productId]);
        $products
            ->setName(isset($data['name']) ? $data['name'] : $products->getName())
            ->setDescription(isset($data['description']) ? $data['description'] : $products->getDescription())
            ->setPhoto(isset($data['photo']) ? $data['photo'] : $products->getPhoto())
            ->setPrice(isset($data['price']) ? $data['price'] : $products->getPrice())
            ->setCategory(isset($data['category']) ? $data['category'] : $products->getCategory());

        try {

            $db = $this->getDoctrine()->getManager();
            $db->persist($products);
            $db->flush();

            return $this->json(
                array('message' => 'Le produit a bien été mis à jour.'),
                Response::HTTP_ACCEPTED
            );
        } catch (Exception $exception) {
            return new JsonResponse(array('message' => 'Une erreur est survenue lors de la mise à jour du produit: ' . $exception), Response::HTTP_BAD_REQUEST);
        }
    }


    /**
     * @Route("/api/product", name="addProduct", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $product = new Product();

        $product
            ->setName($data['name'])
            ->setDescription($data['description'])
            ->setPhoto($data['photo'])
            ->setPrice($data['price'])
            ->setCategory($data['category']);

        try {

            $db = $this->getDoctrine()->getManager();
            $db->persist($product);
            $db->flush();

            return $this->json(
                array('message' => 'Le Produit ' . $product->getName() . ' a bien été crée.'),
                Response::HTTP_CREATED
            );
        } catch (Exception $exception) {
            return new JsonResponse(array('message' => 'Une erreur est survenue lors de la création du produit: ' . $exception), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/product/{productId}", name="deleteProduct", methods={"DELETE"})
     */
    public function delete($productId): JsonResponse
    {
        $product = $this->repository->findOneBy(["id" => $productId]);
        $db = $this->getDoctrine()->getManager();
        $db->remove($product);
        $db->flush();
        return $this->json(["message" => "le produit a bien été suprimmé"]);
    }
}
