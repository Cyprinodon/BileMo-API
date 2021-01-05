<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    /**
     * @Get(path="/products", name="products_list")
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param Serializer $serializer
     * @return JsonResponse
     */
    public function list(Request $request,ProductRepository $productRepository, Serializer $serializer): JsonResponse
    {
        $page = $request->query->get('page');

        $products = $page != null ?
            $productRepository->findAndPaginate('asc',$page):
            $productRepository->findAndPaginate();

        if(!$products) {
            return new JsonResponse(["message" => "Aucun produit trouvé à lister"],404);
        }

        $serializedProducts = $serializer->serialize($products, 'json');
        return new JsonResponse($serializedProducts);
    }

    /**
     * @Get(path="/products/{id}", name="products_show", requirements={"id"="\d+"})
     * @param string $id
     * @param ProductRepository $productRepository
     * @param Serializer $serializer
     * @return JsonResponse
     */
    public function show(string $id, ProductRepository $productRepository, Serializer $serializer) :JsonResponse
    {
        $product = $productRepository->find($id);

        if(!$product) {
            return new JsonResponse(["message" => "Le produit portant l'identifiant '".$id."' n'a pas été trouvé."],404);
        }

        $serializedProduct = $serializer->serialize($product, 'json');
        return new JsonResponse($serializedProduct);
    }
}
