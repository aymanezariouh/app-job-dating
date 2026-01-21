<?php
namespace App\core;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{
    private $viewPath;
    private $twig;
    public function __construct()
    {
        $this->viewPath = __DIR__ . '/../views/';
        $loader = new FilesystemLoader(__DIR__ . '/../views');
        $this->twig = new Environment($loader);
    }
    public function render($template, $data = [])
    {
        $this->twig->display($template . '.twig', $data);
    }
}