<?php
namespace App\core;
abstract class Controller
{
    protected $view;
    protected $auth;
    public function __construct() {
        $this->view = new View();
        $this->auth = new Auth();
    }

    public function render($template , $data = [] ) {
        return $this->view->render($template, $data);
    }
}   