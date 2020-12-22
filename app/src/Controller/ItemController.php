<?php

namespace App\Controller;

use App\Entity\Item;
use App\Entity\User;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/item")
 */
class ItemController extends AbstractController
{
    /**
     * @Route("/", name="item_index", methods={"GET"})
     */
    public function index(ItemRepository $itemRepository): Response
    {
        return $this->render('item/index.html.twig', [
            'items' => $itemRepository->findAll(),
        ]);
    }

    /**
     * @Route("/user/{id}/new", name="user_new_item_todolist", methods={"POST"})
     */
    public function userNewItem(Request $request, $id, UserRepository $userRepo): Response
    {
        $user = $userRepo->findOneBy(["id" => $id]);

        $response = new Response();

        if(!$user){
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $item = new Item();


        $entityManager = $this->getDoctrine()->getManager();

        $item->setName($request->request->get('name'));
        $item->setContent($request->request->get('content'));
        $item->setCreatedAt(new \DateTime());
        $item->setUpdatedAt(new \DateTime());
        $entityManager->persist($item);

        $addResp = $user->addItem($item);
        $entityManager->flush();

        if(gettype($addResp) === "array") {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent(implode(", ", $addResp));
        } elseif(gettype($addResp) === "object") {
            $response->setStatusCode(Response::HTTP_OK);
        }

        $response->send();
        return $response;

    }

    /**
     * @Route("/new", name="item_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($item);
            $entityManager->flush();

            return $this->redirectToRoute('item_index');
        }

        return $this->render('item/new.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="item_show", methods={"GET"})
     */
    public function show(Item $item): Response
    {
        return $this->render('item/show.html.twig', [
            'item' => $item,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="item_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Item $item): Response
    {
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('item_index');
        }

        return $this->render('item/edit.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="item_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Item $item): Response
    {
        if ($this->isCsrfTokenValid('delete'.$item->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($item);
            $entityManager->flush();
        }

        return $this->redirectToRoute('item_index');
    }
}
