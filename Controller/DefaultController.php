<?php

namespace Controller;

use Model\UserManager;

class DefaultController extends BaseController
{
    public function user_accountAction()
    {
        if (!empty($_SESSION['user_id']))
        {
            $manager = UserManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            
            echo $this->renderView('user_account.html.twig',
                                   ['user' => $user]);
        }
        else
            $this->redirect('login');
    }
}
