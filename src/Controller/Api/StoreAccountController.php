<?php

namespace App\Controller\Api;

use App\Entity\StoreAccount;
use App\Repository\StoreAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use OpenApi\Annotations\RequestBody;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OAPI;

class StoreAccountController extends AbstractController
{
    /**
     * @Post("/stores", name="store_account_new")
     *
     * Enregistrement d'un magasin client de Bilemo.
     *
     * Cette requête permet d'ajouter un compte magasin pour s'identifier auprès de l'API.
     *
     * @RequestBody(
     *     required=true,
     *     @OAPI\JsonContent(
     *        @OAPI\Schema(
     *            type="object",
     *            @OAPI\Property(property="name", description="Le prénom du consommateur à ajouter.")
     *            @OAPI\Property(property="email", description="Ll'adresse email du responsable à contacter.")
     *            @OAPI\Property(property="password", description="Le mot de passe à utiliser pour s'autentifier et pouvoir récupérer un jeton d'identification.")
     *        )
     *     ),
     * )
     *
     * @OAPI\Response(
     *     response=400,
     *     description="Requête erronnée. Le corps de la requête contient des informations refusées par le validateur.",
     * )
     *
     * @OAPI\Response(
     *     response=201,
     *     description="Le compte a été ajouté avec succès.",
     * )
     *
     * @OAPI\Tag(name="comptes magasin")
     * @Security(name="Bearer")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function new(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $data = $request->getContent();
        $store = $serializer->deserialize($data, StoreAccount::class, 'json');

        $violations = $validator->validate($store);

        if (0 !== count($violations)) {
            $data = [
                'message' => 'La requête est incorrecte.',
                'violations' => $violations
            ];

            return new JsonResponse($data, 400);
        }

        $entityManager->persist($store);
        $entityManager->flush();

        $data = [ 'message' => 'Le compte a bien été créé.'];
        return new JsonResponse($data, 201);
    }

    /**
     * @Delete("/stores/{id}", name="store_account_delete")
     *
     * Suppression d'un magasin client de Bilemo.
     *
     * Cette requête permet de supprimer un compte magasin de l'API.
     *
     *
     * @OAPI\Response(
     *     response=404,
     *     description="Compte magasin inexistant.",
     * )
     *
     * @OAPI\Response(
     *     response=200,
     *     description="Le compte a été supprimé avec succès.",
     * )
     *
     * @OAPI\Tag(name="comptes magasin")
     * @Security(name="Bearer")
     *
     * @param string $id
     * @param EntityManagerInterface $entityManager
     * @param StoreAccountRepository $storeRepository
     * @return JsonResponse
     */
    public function delete(string $id, EntityManagerInterface $entityManager, StoreAccountRepository $storeRepository)
    {
        //Ajouter la logique d'authentification et vérifier que l'entité appartient bien à l'utilisateur ayant fait la requête

        $store = $storeRepository->find($id);

        if(!$store instanceof StoreAccount) {
            $data = [
                'message' => 'Le compte que vous cherchez à supprimer n\'existe pas.'
            ];

            return new JsonResponse($data, 404);
        }

        $this->denyAccessUnlessGranted($store);

        $entityManager->remove($store);
        $entityManager->flush();

        $data = [
            'message' => 'Le compte a été supprimé avec succès.'
        ];

        return new JsonResponse($data, 200);
    }
}
