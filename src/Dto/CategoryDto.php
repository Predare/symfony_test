<?php

namespace App\Dto;

use App\Entity\Category;

class CategoryDto
{
    public function __construct(private $id, private $title)
    {
        $this -> id = $id;
        $this -> title = $title;
    }

        /**
         * Get the value of title
         */ 
        public function getTitle()
        {
                return $this->title;
        }

        public function toEntity(){
            return new Category(
                $this->id,
                $this->title
            );
        }
}