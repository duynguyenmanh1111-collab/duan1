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
    public function contact()
    {
        require_once PATH_VIEW . 'contact.php';
    }
    public function services()
    {
        require_once PATH_VIEW . 'services.php';
    }
    public function blog()
    {
        require_once PATH_VIEW . 'blog.php';
    }
}
