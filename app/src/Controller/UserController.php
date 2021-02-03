<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/list", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $users = $userRepository->findAll();
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_OK);
        $response->setData(count($users));

        return $response;
    }

    /**
     * @Route("/new", name="user_new", methods={"POST"})
     */
    public function new(Request $request, SerializerInterface $serializer): Response
    {
        $user = new User();

        $entityManager = $this->getDoctrine()->getManager();
        $response = new JsonResponse();

        $user->setFirstName($request->request->get('first_name'));
        $user->setLastName($request->request->get('last_name'));
        $user->setEmail($request->request->get('email'));
        $user->setPassword($request->request->get('password'));
        $user->setBirthdate(new \DateTime($request->request->get('birthdate')));

        $entityManager->persist($user);
        $entityManager->flush();

        $response->setStatusCode(Response::HTTP_CREATED);

        return $response;
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(UserRepository $userRepo, $id): Response
    {
        $user = $userRepo->findOneBy(["id" => $id]);

        $response = new JsonResponse();

        if(!$user){
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->setContent("User doesn't exist !");
            return $response;
        }

        $response->setStatusCode(Response::HTTP_OK);
        $response->setData($user->getId());

        return $response;
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"PUT"})
     */
    public function edit(Request $request, UserRepository $userRepo, $id, SerializerInterface $serializer): Response
    {
        $user = $userRepo->findOneBy(["id" => $id]);

        $response = new JsonResponse();

        if(!$user){
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->setContent("User doesn't exist !");
            return $response;
        }

        $entityManager = $this->getDoctrine()->getManager();

        $user->setFirstName($request->request->get('first_name'));
        $user->setLastName($request->request->get('last_name'));
        $user->setEmail($request->request->get('email'));
        $user->setPassword($request->request->get('password'));
        $user->setBirthdate(new \DateTime($request->request->get('birthdate')));

        $entityManager->flush();

        $response->setStatusCode(Response::HTTP_OK);
        $response->setData($serializer->normalize($user, null));

        return $response;

    }

    /**
     * @Route("/delete/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, $id, UserRepository $userRepo): Response
    {
        $user = $userRepo->findOneBy(["id" => $id]);

        $response = new JsonResponse();

        if(!$user){
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->setContent("User doesn't exist !");
            return $response;
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($user);
        $entityManager->flush();

        $response->setStatusCode(Response::HTTP_NO_CONTENT);
        return $response;

    }
}
