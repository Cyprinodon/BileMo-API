<?php

namespace App\Controller\Api;

use App\Entity\Customer;
use App\Entity\StoreAccount;
use App\Repository\CustomerRepository;
use App\Repository\StoreAccountRepository;
use App\Service\CacheableResponse;
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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OAPI;

class CustomerController extends AbstractController
{
    private const DEFAULT_HEADER = ['Content-Type' => 'application/json'];

    /**
     * @Get(path="/stores/{storeId}/customers", name="customers_list", requirements={"id"="\d+"})
     *
     * Liste des consommateurs d'un magasin client de Bilemo.
     *
     * Cette requête récupère une liste paginée simplifiée de tous les clients du magasin portant l'id spécifié.
     * Le magasin demandé doit correspondre au compte magasin connecté.
     *
     * @OA\Response(
     *     response=200,
     *     description="Renvoie une page de la liste de tous les consommateurs du magasin spécifié (5 consommateurs par page). Le magasin spécifié doit correspondre au magasin connecté effectuant la requête.",
     *     @OAPI\JsonContent(
     *        type="array",
     *        @OAPI\Items(ref=@Model(type=Customer::class, groups={"list"}))
     *     )
     * )
     * @OAPI\Parameter(
     *     name="page",
     *     in="query",
     *     description="Champ permettant de sélectionner une page spécifique. Si non renseigné, la première page sera renvoyée par défaut.",
     *     @OAPI\Schema(type="string")
     * )
     * @OAPI\Tag(name="consommateurs")
     * @Security(name="Bearer")
     *
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

        return new CacheableResponse($serializedCustomers, 200, self::DEFAULT_HEADER);
    }

    /**
     * @Get(path="stores/{storeId}/customers/{customerId}", name="customers_show", requirements={"id"="\d+"})
     *
     * Détails d'un consommateurs lié à un magasin client de Bilemo.
     *
     * Cette requête récupère les informations détaillées d'un client spécifique du magasin portant l'id demandé.
     * Le magasin demandé doit correspondre au compte magasin connecté.
     *
     * @OAPI\Response(
     *     response=200,
     *     description="Renvoie les informations du consommateur d'un magasin spécifié. Le magasin spécifié doit correspondre au magasin connecté effectuant la requête.",
     *     @OAPI\JsonContent(
     *        @Model(type=Customer::class, groups={"show"})
     *     )
     * )
     *
     * @OAPI\Response(
     *     response=404,
     *     description="Magasin demandé non trouvé.",
     * )
     *
     * @OAPI\Response(
     *     response=404,
     *     description="Consommateur demandé non trouvé.",
     * )
     *
     * @OAPI\Response(
     *     response=403,
     *     description="Accès refusé. L'id du magasin ne corresponds pas à celui du magasin connecté.",
     * )
     *
     * @OAPI\Response(
     *     response=401,
     *     description="Accès non authorisé. Les informations d'authentification sont manquantes ou erronnées.",
     * )
     *
     * @OAPI\Tag(name="consommateurs")
     * @Security(name="Bearer")
     *
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

        return new CacheableResponse($serializedCustomer, 200, self::DEFAULT_HEADER);
    }

    /**
     * @Post(path="stores/{storeId}/customers", name="customers_new", requirements={"storeId"="\d+"})
     *
     * Ajout d'un consommateurs lié à un magasin client de Bilemo.
     *
     * Cette requête permet d'ajouter un nouveau consommateur lié au magasin portant l'id demandé.
     * Le magasin demandé doit correspondre au compte magasin connecté.
     *
     * @OAPI\RequestBody(
     *     required=true,
     *     @OAPI\JsonContent(
     *        @OAPI\Schema(
     *            type="object",
     *            @OAPI\Property(property="firstName", description="Le prénom du consommateur à ajouter.")
     *            @OAPI\Property(property="lastName", description="Le nom du consommateur à ajouter.")
     *        )
     *     ),
     * )
     *
     * @OAPI\Response(
     *     response=201,
     *     description="Le consommateur a été ajouté avec succcès.",
     * )
     *
     * @OAPI\Response(
     *     response=404,
     *     description="Magasin demandé non trouvé.",
     * )
     *
     * @OAPI\Response(
     *     response=409,
     *     description="Le consommateur existe déjà en base de données.",
     * )
     *
     * @OAPI\Response(
     *     response=403,
     *     description="Accès refusé. L'id du magasin ne corresponds pas à celui du magasin connecté.",
     * )
     *
     * @OAPI\Response(
     *     response=401,
     *     description="Accès non authorisé. Les informations d'authentification sont manquantes ou erronnées.",
     * )
     *
     * @OAPI\Tag(name="consommateurs")
     * @Security(name="Bearer")
     *
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

        $this->denyAccessUnlessGranted("access", $store);

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
     * @Delete(path="stores/{storeId}/customers/{customerId}", name="customers_delete", requirements={"storeId"="\d+", "customerId"="\d+"})
     *
     * Suppression d'un consommateurs lié à un magasin client de Bilemo.
     *
     * Cette requête permet de supprimer le consommateur désiré lié au magasin portant l'id demandé.
     * Le magasin demandé doit correspondre au compte magasin connecté.
     *
     * @OAPI\Response(
     *     response=200,
     *     description="Le consommateur a bien été supprimé.",
     * )
     *
     * @OAPI\Response(
     *     response=404,
     *     description="Magasin demandé non trouvé.",
     * )
     *
     * @OAPI\Response(
     *     response=404,
     *     description="Consommateur demandé non trouvé pour le magasin renseigné.",
     * )
     *
     * @OAPI\Response(
     *     response=403,
     *     description="Accès refusé. L'id du magasin ne corresponds pas à celui du magasin connecté.",
     * )
     *
     * @OAPI\Response(
     *     response=401,
     *     description="Accès non authorisé. Les informations d'authentification sont manquantes ou erronnées.",
     * )
     *
     * @OAPI\Tag(name="consommateurs")
     * @Security(name="Bearer")
     *
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
