<?php

namespace App\Controller\Api;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends AbstractController
{
    private const MISSING_STORE_PARAMETER_MESSAGE = "Le paramètre obligatoire 'store' est mal renseigné ou inexistant. Identifiant numérique attendu (ex: store=1).";
    private const DEFAULT_HEADER = ['Content-Type' => 'application/json'];
    /**
     * @Get(path="/customers", name="customers_list")
     * @param Request $request
     * @param CustomerRepository $customerRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function list(
        Request $request,
        CustomerRepository $customerRepository,
        SerializerInterface $serializer)
    {
        $store = $request->query->get('store');
        $page = $request->query->get('page');

        if(is_null($store)) {
            $body = ["message" => self::MISSING_STORE_PARAMETER_MESSAGE];
            return new JsonResponse($body, 400, self::DEFAULT_HEADER);
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
     * @Get(path="/customers/{id}", name="customers_show", requirements={"id"="\d+"})
     * @param string $id
     * @param Request $request
     * @param CustomerRepository $customerRepository
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function show(
        string $id,
        Request $request,
        CustomerRepository $customerRepository,
        SerializerInterface  $serializer)
    {
        $store = $request->query->get('store');

        if(is_null($store)) {
            $body = ["message" => self::MISSING_STORE_PARAMETER_MESSAGE];
            return new JsonResponse($body, 400, self::DEFAULT_HEADER);
        }

        /*Vérifier que l'utilisateur authentifié corresponds à 'store'. S'il ne l'est pas, retourner un code 403 (forbidden)*/

        $customer = $customerRepository->findFromStore($id, $store);

        if(!$customer instanceof Customer) {
            return new JsonResponse(["message" => "Le consommateur portant l'identifiant '".$id."' n'a pas été trouvé pour le magasin '".$store."'."],404);
        }

        $serializationGroup = SerializationContext::create()->setGroups('show');
        $serializedCustomer = $serializer->serialize($customer, 'json', $serializationGroup);

        return new Response($serializedCustomer, 200, self::DEFAULT_HEADER);
    }
}
