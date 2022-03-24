<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class BookHandler implements MessageHandlerInterface
{
    public function __invoke(Book $book)
    {
        dump($book->getName());
//        $product = new Product();
//        $product->setName($book->getName());
//        $product->setDescription($book->getAuthor());
//        $product->setKeywords('book');

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_POSTFIELDS, array(
            CURLOPT_URL => '127.0.0.1:8000/rmq/save',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($book)));
        $response = curl_exec($curl);
        curl_close($curl);
    }
}