<?php

namespace Controller;

use Model\UserManager;
use Model\FilesManager;

class DefaultController extends BaseController
{
    public function user_accountAction()
    {
        if (!empty($_SESSION['user_id']))
        {
            $manager = UserManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            $fileManager = FilesManager::getInstance();
            $allFiles = $fileManager->showFiles($_SESSION['user_id']);
            echo $this->renderView('user_account.html.twig',
                                   ['user' => $user, 'allFiles' => $allFiles]);
        }
        else
            $this->redirect('login');
    }

    public function profilAction()
    {
        if (!empty($_SESSION['user_id'])){
            $manager = UserManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            echo $this->renderView('profil.html.twig',
                ['user' => $user]);
        }
        else
            $this->redirect('login');
    }
}
