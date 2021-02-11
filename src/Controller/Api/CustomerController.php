<?php

namespace App\Controller\Api;

use App\Entity\Customer;
use App\Entity\StoreAccount;
use App\Repository\CustomerRepository;
use App\Repository\StoreAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTime;

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

        $this->denyAccessUnlessGranted($store);

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

        $this->denyAccessUnlessGranted($store);

        $customer = $customerRepository->findFromStore($customerId, $store);

        if(!$customer instanceof Customer) {
            return new JsonResponse(["message" => "Le consommateur portant l'identifiant '".$customerId."' n'a pas été trouvé pour le magasin '".$store->getId()."'."],404);
        }

        $serializationGroup = SerializationContext::create()->setGroups('show');
        $serializedCustomer = $serializer->serialize($customer, 'json', $serializationGroup);

        return new Response($serializedCustomer, 200, self::DEFAULT_HEADER);
    }

    /**
     * @Post(path="stores/{storeId}/customers", name="customers_new", requirements={"storeId"="\d+"})
     * @param Request $request
     * @param string $storeId
     * @param EntityManagerInterface $entityManager
     * @param StoreAccountRepository $storeRepository
     * @param CustomerRepository $customerRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function new(
        Request $request,
        string $storeId,
        EntityManagerInterface $entityManager,
        StoreAccountRepository $storeRepository,
        CustomerRepository $customerRepository,
        SerializerInterface $serializer):JsonResponse
    {
        $store = $storeRepository->find($storeId);

        if(!$store instanceof StoreAccount) {
            return new JsonResponse(["message" => "Le magasin n°".$storeId." n'a pas été trouvé."], 404);
        }

        $this->denyAccessUnlessGranted($store);

        $data = $request->getContent();
        $customer = $serializer->deserialize($data, Customer::class, 'json');

        $existingCustomer = $customerRepository->findOneBy([
            "firstname" => $customer->getFirstName(),
            "lastName" => $customer->getLastName()
        ]);

        if($existingCustomer instanceof Customer) {
            return new JsonResponse(["message" => "Ce consommateur existe déjà pour le magasin ".$storeId."."], 409);
        }

        $customer->setStoreAccount($store);
        $customer->setCreatedAt(new DateTime);
        $entityManager->persist($customer);
        $entityManager->flush();

        $data = ["message" => "Le consommateur '".$customer->getFirstName()." ".$customer->getLastName()."' a été créé avec succès et lié au magasin ".$storeId."."];
        return new JsonResponse($data, 201);
    }

    /**
     * @Delete(path="stores/{storeId}/customers/{customerId}", name="customers_delete, requirements={"storeId"="\d+", "customerId"="\d+"})
     * @param string $storeId
     * @param string $customerId
     * @param EntityManagerInterface $entityManager
     * @param StoreAccountRepository $storeRepository
     * @param CustomerRepository $customerRepository
     * @return JsonResponse
     */
    public function delete(
        string $storeId,
        string $customerId,
        EntityManagerInterface $entityManager,
        StoreAccountRepository $storeRepository,
        CustomerRepository $customerRepository)
    {
        $store = $storeRepository->find($storeId);

        if(!$store instanceof StoreAccount) {
            return new JsonResponse(["message" => "Le magasin n°".$storeId." n'a pas été trouvé."], 404);
        }

        $this->denyAccessUnlessGranted($store);

        $customer = $customerRepository->findFromStore($customerId, $store);

        if(!$customer instanceof Customer) {
            return  new JsonResponse(["message" => "Le consommateur n°".$customerId." n'a pas été trouvé pour le magasin ".$storeId."."], 404);
        }

        $entityManager->remove($customer);
        $entityManager->flush();

        return new JsonResponse(["message" => "Le consommateur n°".$customerId." a été supprimé avec succès"], 200);
    }
}
