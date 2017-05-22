<?php

namespace Controller;

use Model\UserManager;
use Model\FilesManager;

class FilesController extends BaseController
{
    public function upload_fileAction()
    {
        if (!empty($_SESSION['user_id'])){
            $manager = UserManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                if(isset($_FILES['uploaded_file'])){
                    $manager = FilesManager::getInstance();
                    if(empty($manager->checkUploadFile($_FILES['uploaded_file'], $_POST))){
                        $manager->uploadFile($_FILES['uploaded_file'], $_POST);
                        $this->redirect('user_account');
                    }
                    else{
                        $errors = $manager->checkUploadFile($_FILES['uploaded_file'], $_POST);
                        echo $this->renderView('user_account.html.twig',
                                    ['user' => $user, 'errors' => $errors]);
                    }
                }
            }
        }
        else{
            $this->redirect('login');
        }
    }

    public function uploadAction()
    {
        if (!empty($_SESSION['user_id'])){
            $manager = UserManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            $fileManager = FilesManager::getInstance();
            $allFiles = $fileManager->showFiles($_SESSION['user_id']);
            echo $this->renderView('upload.html.twig', ['user' => $user, 'allFiles' => $allFiles]);
        }
        else{
            $this->redirect('login');
        }     
    }

    public function your_filesAction()
    {
        if (!empty($_SESSION['user_id'])){
            echo $this->renderView('your_files.html.twig');
        }
        else
            $this->redirect('login');
    }

}
