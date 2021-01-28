<?php

namespace App\Controller\Api;

use App\Entity\StoreAccount;
use App\Repository\StoreAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class StoreAccountController extends AbstractController
{
    /**
     * @Post("/stores", name="store_account_new")
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
     * @param Request $request
     * @param string $id
     * @param EntityManagerInterface $entityManager
     * @param StoreAccountRepository $storeRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function delete(Request $request, string $id, EntityManagerInterface $entityManager, StoreAccountRepository $storeRepository, SerializerInterface $serializer)
    {
        //Ajouter la logique d'authentification et vérifier que l'entité appartient bien à l'utilisateur ayant fait la requête

        $store = $storeRepository->find($id);

        if(!$store instanceof StoreAccount) {
            $data = [
                'message' => 'Le compte que vous cherchez à supprimer n\'existe pas.'
            ];

            return new JsonResponse($data, 404);
        }

        $entityManager->remove($store);
        $entityManager->flush();

        $data = [
            'message' => 'Le compte a été supprimé avec succès.'
        ];

        return new JsonResponse($data, 200);
    }
}
