<?php
namespace App\controllers\back;

use App\core\Controller;

class AdminController extends Controller
{
    public function dashboard()
    {
        return $this->render('admin/dashboard', [
            'title' => 'Admin Dashboard'
        ]);
    }
}