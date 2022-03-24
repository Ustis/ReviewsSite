<?php

namespace App\Service;

class Book
{
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }
    private $id;
    private $name;
    private $author;

//    /**
//     * @param $name
//     * @param $author
//     */
//    public function __construct($name, $author)
//    {
//        $this->name = $name;
//        $this->author = $author;
//    }

    public function getContent()
    {
        return $this;
    }
}