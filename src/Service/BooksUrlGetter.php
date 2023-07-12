<?php

namespace App\Service;

class BooksUrlGetter
{
    public string $url;
    public function __construct($url)
    {
        $this->url = $url;
    }
}