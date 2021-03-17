<?php

namespace App\Service;

use Symfony\Component\String\Slugger\AsciiSlugger;

class Slugger
{
    private $slugger;

    public function __construct($lang='fr')
    {
        $this->slugger = new AsciiSlugger($lang);
    }

    public function slugify($string)
    {
        return strtolower($this->slugger->slug($string));
    }
}

/*
use Symfony\Component\String\Slugger\SluggerInterface;

class MyService
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function slugify()
    {
        $slug = $this->slugger->slug('...');
        //retirer les espaces avant et après
        //$name->trim();
        //str_replace('', '_', $name);
        //strtolower();
    }
}
*/