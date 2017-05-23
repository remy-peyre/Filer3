<?php

namespace Controller;

use Model\UserManager;

class SecurityController extends BaseController
{
    public function loginAction()
    {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $manager = UserManager::getInstance();
            if ($manager->userCheckLogin($_POST))
            {
                $manager->userLogin($_POST['username']);
                $this->redirect('user_account');
            }
            else {
                $errors = $manager->userCheckLogin($_POST);
                echo $this->renderView('login.html.twig', ['errors' => $errors,]);
            }
        }
        else{
            echo $this->renderView('login.html.twig');
        }
    }

    public function logoutAction()
    {
        session_destroy();
        echo $this->redirect('login');
    }

    public function registerAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            $manager = UserManager::getInstance();
            if ($manager->userCheckRegister($_POST))
            {
                var_dump($manager->userCheckRegister($_POST));
                $manager->userRegister($_POST);
                $this->redirect('login');
            }
            else {
               $errors = $manager->userCheckRegister($_POST);
               echo $this->renderView('register.html.twig', ['errors' => $errors,]);
            }    
        }
        else{
            echo $this->renderView('register.html.twig');
        }
    }
}
