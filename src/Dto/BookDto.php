<?php

namespace App\Dto;

use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;

class BookDto
{

    public function __construct(
        private $title = '',
        private $isbn = '',
        private $pageCount = '',
        private $publishedDate = [],
        private $thumbnailUrl = '',
        private $shortDescription = '',
        private $longDescription = '',
        private $status = '',
        private array $authors = [],
        private array $categories = []
    ){
        $this->title = $title;
        $this->isbn = $isbn;
        $this->pageCount = $pageCount;
        $this->publishedDate = $publishedDate;
        $this->thumbnailUrl = $thumbnailUrl;
        $this->shortDescription = $shortDescription;
        $this->longDescription = $longDescription;
        $this->status = $status;
        $this->authors = $authors;
        $this->categories = $categories;
    }

    public function __toString()
    {
        return $this->title;
    }

    public function toEntity(){
        $book = new Book();
        $book -> setTitle($this->title);
        $book -> setIsbn($this->isbn);
        $book -> setPageCount($this->pageCount);
        $book -> setPublishedDate($this->publishedDate);
        $book -> setThumbnailUrl($this->thumbnailUrl);
        $book -> setShortDescription($this->shortDescription);
        $book -> setLongDescription($this->longDescription);
        $book -> setStatus($this->status);
        $book -> setAuthors($this->authors);
        return $book;
    }

        /**
         * Get the value of isbn
         */ 
        public function getIsbn()
        {
                return $this->isbn;
        }

        public function getCategories(){
            return $this->categories;
        }

        /**
         * Set the value of categories
         *
         * @return  self
         */ 
        public function setCategories($categories)
        {
                $this->categories = $categories;

                return $this;
        }

        /**
         * Get the value of title
         */ 
        public function getTitle()
        {
                return $this->title;
        }
}