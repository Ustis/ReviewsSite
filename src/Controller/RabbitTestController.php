<?php

namespace App\Controller;

use App\Service\Book;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rmq')]
class RabbitTestController extends AbstractController
{
    #[Route('/new', name: 'new_book', methods: ['GET'])]
    public function new(MessageBusInterface $bus): Response
    {
        $book = new Book();
        $book->setName('Name');
        $book->setAuthor('Author');

        $bus->dispatch($book);

        return $this->redirectToRoute('about');
    }


    /**
     * @param PostRepository $postRepository
     * @Route("/save", name="posts", methods={"GET", "POST"})
     */
    public function getBook(Request $request){
        dump($request);
        return $this->response();
    }
//    #[Route('/new', name: 'new_book', methods: ['GET'])]
//    public function new(MessageBusInterface $bus): Response
//    {
//        $book = new Book();
//        $book->setName('Name');
//        $book->setAuthor('Author');
//
//        $bus->dispatch($book);
//
//        return $this->redirectToRoute('about');
//    }
}