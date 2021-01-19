<?php

namespace App\Controller\Api;

use App\Entity\Customer;
use App\Entity\StoreAccount;
use App\Repository\CustomerRepository;
use App\Repository\StoreAccountRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends AbstractController
{
    private const DEFAULT_HEADER = ['Content-Type' => 'application/json'];

    /**
     * @Get(path="/stores/{storeId}/customers", name="customers_list", requirements={"id"="\d+"})
     * @param Request $request
     * @param string $storeId
     * @param CustomerRepository $customerRepository
     * @param StoreAccountRepository $storeRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function list(
        string $storeId,
        Request $request,
        CustomerRepository $customerRepository,
        StoreAccountRepository $storeRepository,
        SerializerInterface $serializer)
    {
        $page = $request->query->get('page');

        $store = $storeRepository->find($storeId);

        if(!$store instanceof StoreAccount) {
            $body = ["message" => "Aucun Compte client n'a été trouvé pour l'identifiant '".$storeId."'."];
            return new JsonResponse($body, 404, self::DEFAULT_HEADER);
        }

        $customers = !is_null($page) ?
            $customerRepository->findByStoreAccountAndPaginate($store, $page):
            $customerRepository->findByStoreAccountAndPaginate($store);

        $pagerIterator = $customers->getIterator();
        $customers = iterator_to_array($pagerIterator);

        $serializationGroup = SerializationContext::create()->setGroups('list');
        $serializedCustomers = $serializer->serialize($customers, 'json', $serializationGroup);

        return new Response($serializedCustomers, 200, self::DEFAULT_HEADER);
    }

    /**
     * @Get(path="stores/{storeId}/customers/{customerId}", name="customers_show", requirements={"id"="\d+"})
     * @param string $storeId
     * @param string $customerId
     * @param CustomerRepository $customerRepository
     * @param StoreAccountRepository $storeRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function show(
        string $storeId,
        string $customerId,
        CustomerRepository $customerRepository,
        StoreAccountRepository $storeRepository,
        SerializerInterface  $serializer)
    {
        $store = $storeRepository->find($storeId);

        if(!$store instanceof StoreAccount) {
            $body = ["message" => "Aucun compte client n'a été trouvé pour l'identifiant '".$storeId."'."];
            return new JsonResponse($body, 404, self::DEFAULT_HEADER);
        }

        /*Vérifier que l'utilisateur authentifié corresponds à 'store'. S'il ne l'est pas, retourner un code 403 (forbidden)*/

        $customer = $customerRepository->findFromStore($customerId, $store);

        if(!$customer instanceof Customer) {
            return new JsonResponse(["message" => "Le consommateur portant l'identifiant '".$customerId."' n'a pas été trouvé pour le magasin '".$store->getId()."'."],404);
        }

        $serializationGroup = SerializationContext::create()->setGroups('show');
        $serializedCustomer = $serializer->serialize($customer, 'json', $serializationGroup);

        return new Response($serializedCustomer, 200, self::DEFAULT_HEADER);
    }
}
