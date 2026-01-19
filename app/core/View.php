<?php
namespace App\core;

class View
{
    private $viewPath;
    
    public function __construct()
    {
        $this->viewPath = __DIR__ . '/../views/';
    }
    public function render($template, $data = [])
    {   
        $templateFile = $this->viewPath . $template . '.php';
        if (!file_exists($templateFile)) {
            throw new \Exception("{$template} not found");
        }
        extract($data);
        ob_start();
        include $templateFile;
        return ob_get_clean();
    }
}