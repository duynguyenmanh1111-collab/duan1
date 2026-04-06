<?php

class HomeController
{
    public function index() 
    {
        require_once PATH_VIEW . 'main.php';
    }
    public function about() 
    {
        require_once PATH_VIEW . 'about.php';
    }
}
