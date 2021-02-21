<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CacheableResponse;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    private const DEFAULT_HEADER = ['Content-Type' => 'application/json'];
    /**
     * @Get(path="/products", name="products_list")
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function list(Request $request,ProductRepository $productRepository, SerializerInterface $serializer): Response
    {
        $page = $request->query->get('page');

        $products = is_int($page) ?
            $productRepository->findAllAndPaginate($page):
            $productRepository->findAllAndPaginate();

        $pagerIterator = $products->getIterator();
        $products = iterator_to_array($pagerIterator);

        $serializationGroup = SerializationContext::create()->setGroups('list');
        $serializedProducts = $serializer->serialize($products, 'json', $serializationGroup);

        return new CacheableResponse($serializedProducts, 200, self::DEFAULT_HEADER);
    }

    /**
     * @Get(path="/products/{id}", name="products_show", requirements={"id"="\d+"})
     * @param string $id
     * @param ProductRepository $productRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function show(string $id, ProductRepository $productRepository, SerializerInterface $serializer) :Response
    {
        $product = $productRepository->find($id);

        if(!$product instanceof Product) {
            return new JsonResponse(["message" => "Le produit portant l'identifiant '".$id."' n'a pas été trouvé."], 404);
        }
        $serializationGroup = SerializationContext::create()->setGroups('show');
        $serializedProduct = $serializer->serialize($product, 'json', $serializationGroup);

        return new CacheableResponse($serializedProduct, 200, self::DEFAULT_HEADER);
    }
}
