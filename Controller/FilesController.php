<?php

namespace Controller;

use Model\UserManager;
use Model\FilesManager;

class FilesController extends BaseController
{

    public function uploadAction()
    {
        if (!empty($_SESSION['user_id'])){
            $manager = UserManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                if(!empty($_POST['upload_button'])){
                    $manager = FilesManager::getInstance();
                    if(empty($manager->checkUploadFile($_FILES['uploaded_file'], $_POST))){
                        $manager->uploadFile($_FILES['uploaded_file'], $_POST);
                        $this->redirect('your_files');
                    }
                    else{
                        $errors = $manager->checkUploadFile($_FILES['uploaded_file'], $_POST);
                        echo $this->renderView('upload.html.twig',
                                    ['user' => $user, 'errors' => $errors]);
                    }
                }
            }
            else{
                echo $this->renderView('upload.html.twig',
                                        ['user' => $user]);
            }
        }
        else{
            $this->redirect('login');
        }  
    }

    public function your_filesAction()
    {
        if (!empty($_SESSION['user_id'])){
            $manager = UserManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            $fileManager = FilesManager::getInstance();
            $allFiles = $fileManager->showFiles($_SESSION['user_id']);
            echo $this->renderView('your_files.html.twig',
                        ['user' => $user, 'allFiles' => $allFiles]);
        }
        else
            $this->redirect('login');
    }

}
