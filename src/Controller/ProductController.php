<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
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
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function list(Request $request,ProductRepository $productRepository, SerializerInterface $serializer): JsonResponse
    {
        $page = $request->query->get('page');

        $products = $page != null ?
            $productRepository->findAndPaginate('asc',$page):
            $productRepository->findAndPaginate();
        $pagerIterator = $products->getIterator();
        $products = iterator_to_array($pagerIterator);
        $serializationGroup = SerializationContext::create()->setGroups('list');
        $serializedProducts = $serializer->serialize($products, 'json', $serializationGroup);
        return new JsonResponse($serializedProducts);
    }

    /**
     * @Get(path="/products/{id}", name="products_show", requirements={"id"="\d+"})
     * @param string $id
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function show(string $id, ProductRepository $productRepository, SerializerInterface $serializer) :JsonResponse
    {
        $product = $productRepository->find($id);

        if(!$product instanceof Product) {
            return new JsonResponse(["message" => "Le produit portant l'identifiant '".$id."' n'a pas été trouvé."],404);
        }
        $serializedProduct = $serializer->serialize($product, 'json');

        return new JsonResponse($serializedProduct);
    }
}
