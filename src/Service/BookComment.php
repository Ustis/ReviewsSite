<?php

namespace App\Service;

class BookComment
{
    /**
     * @return mixed
     */
    public function getBookName()
    {
        return $this->bookName;
    }

    /**
     * @param mixed $bookName
     */
    public function setBookName($bookName): void
    {
        $this->bookName = $bookName;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCreatorName()
    {
        return $this->creatorName;
    }

    /**
     * @param mixed $creatorName
     */
    public function setCreatorName($creatorName): void
    {
        $this->creatorName = $creatorName;
    }

    /**
     * @return mixed
     */
    public function getCommentText()
    {
        return $this->commentText;
    }

    /**
     * @param mixed $commentText
     */
    public function setCommentText($commentText): void
    {
        $this->commentText = $commentText;
    }
    private $bookName;
    private $description;
    private $creatorName;
    private $commentText;

    /**
     * @param $bookName
     * @param $description
     * @param $creatorName
     * @param $commentText
     */
    public function __construct($bookName, $description, $creatorName, $commentText)
    {
        $this->bookName = $bookName;
        $this->description = $description;
        $this->creatorName = $creatorName;
        $this->commentText = $commentText;
    }

    public function getContent()
    {
        return $this;
    }
}