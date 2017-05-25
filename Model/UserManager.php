<?php

namespace Model;

class UserManager
{
    private $DBManager;
    private $FilesManager;
    
    private static $instance = null;
    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new UserManager();
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->DBManager = DBManager::getInstance();
    }

    public function getUserById($id)
    {
        $id = (int)$id;
        $data = $this->DBManager->findOne("SELECT * FROM users WHERE id = ".$id);
        return $data;
    }
    
    public function getUserByUsername($username)
    {
        $data = $this->DBManager->findOneSecure("SELECT * FROM users WHERE username = :username",
                                ['username' => $username]);
        return $data;
    }

    public function getUserByEmail($email)
    {
        $data = $this->DBManager->findOneSecure("SELECT * FROM users WHERE email = :email",
                                ['email' => $email]);
        return $data;
    }
    
    public function userCheckRegister($data)
    {
        $errors = array();
        if (empty($data['username']) OR empty($data['email']) OR empty($data['password'])){
            $errors['fields'] = "Missing Fields";
        }

        $checkEmail = $this->getUserByEmail($data['email']);
        if($checkEmail !== false){
            $errors['email'] = 'email already used';
        }
    
        $checkUsername = $this->getUserByUsername($data['username']);
        if($checkUsername !== false){
            $errors['username'] = 'Username already used';
        }

        if(!$this->emailValid($data['email'])){
            $errors['email'] = "Email not conform";
        }

        if(!isset($data['password']) || !$this->passwordValid($data['password'])){
            $errors['password'] = "Your password must have at least 6 characters, one number and one uppercase";
        }

        if( $data['password'] != $data['verif_password']){
            $errors['password'] = "Password doesn't match";
        }
        return $errors;
    }

    private function emailValid($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    //Minimum : 6 caractères avec au moins une lettre majuscule et un nombre
    private function passwordValid($password){
        return preg_match('`^([a-zA-Z0-9-_]{6,20})$`', $password);
    }
    
    private function userHash($pass)
    {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        return $hash;
    }
    
    public function userRegister($data)
    {
        $user['username'] = $data['username'];
        $user['email'] = $data['email'];
        $user['password'] = $this->userHash($data['password']);
        $user['firstname'] = $data['firstname'];
        $user['lastname'] = $data['lastname'];
        $this->DBManager->insert('users', $user);
        $user = $this->getUserByUsername($user['username']);
        mkdir("uploads/". $user['id']);
        $text = $user['username'] . " just registered ! ";
        $this->watchActionLog("access.log", $text);
    }
    
    public function userCheckLogin($data)
    {
        $errors = array();
        if (empty($data['username']) OR empty($data['password'])){
            $errors['fields'] = "Missing fields";
        }
        $user = $this->getUserByUsername($data['username']);
        if ($user === false){
            $errors['username'] = "Unknown username";
        }
        else{
            if (!password_verify($data['password'], $user['password']))
            {
                $errors['password'] = "Password doesn't match with this account";
            }
        }
        return $errors;
    }
    
    public function userLogin($username)
    {
        $data = $this->getUserByUsername($username);
        if ($data === false)
            return false;
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['current_folder'] = 0;
        return true;
    }

    public function giveMeDate(){
        $date = date("d-m-Y");
        $heure = date("H:i");
        return $date . " " . $heure;
    }

    public function watchActionLog($file, $text){
        $date = $this->giveMeDate();
        $line = $date . " || => " . $text . '\n';
        $file_log = fopen('logs/' . $file, 'a');
        fwrite($file_log, $line);
        fclose($file_log);
    }

}
