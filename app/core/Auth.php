<?php
namespace App\core;

use App\core\Security;
use App\core\Validator;
use App\models\User;
class Auth
{
    private $db;
    private $errors = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    public function isLoggedIn()
    {
        return Session::get('userId');
    }
    public function errors()
    {
        return $this->errors;
    }
    public function login($email, $password)
    {
        $userObj = new User();
        $user = $userObj->findByEmail($email);
        if(!empty($user) && Security::verify($password, $user['password']))
        {
            Session::set('userId',$user['id']);
            return true;
        }
        $this->errors[] = "Invalid email or password.";
        return false;
    }
    public function logout()
    {
        Session::destroy();
    }
    public function register($name, $email, $password)
    {
        $validObj = new Validator(['name'=>$name,'email'=>$email,'password'=>$password]);
        $validObj->required(['name','email','password'])
                 ->min('name',3)
                 ->email('email')
                 ->min('password',8);
        
        if($validObj->fails())
        {
            $this->errors = $validObj->errors();
            return false;
        }
        
        $userObj = new User();
        $user = $userObj->findByEmail($email);
        
        if($user)
        {
            $this->errors['email'] = 'Email already exists';
            return false;
        }
        
        $hashedPassword = Security::hash($password);
        $userObj->create(['name'=>$name, 'email'=>$email, 'password'=>$hashedPassword]);
        
        return true;
    }
}