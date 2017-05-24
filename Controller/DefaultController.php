<?php

namespace Controller;

use Model\UserManager;
use Model\FilesManager;

class DefaultController extends BaseController
{
    public function userAccountAction()
    {
        if (!empty($_SESSION['user_id']))
        {
            $manager = UserManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            $fileManager = FilesManager::getInstance();
            $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
            echo $this->renderView('userAccount.html.twig',
                                   ['user' => $user, 'allFiles' => $allFiles]);
        }
        else
            $this->redirect('login');
    }

}
