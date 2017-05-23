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
                    $fileManager = FilesManager::getInstance();
                    if(empty($fileManager->checkUploadFile($_FILES['uploaded_file'], $_POST))){
                        $fileManager->uploadFile($_FILES['uploaded_file'], $_POST);
                        $this->redirect('your_files');
                    }
                    else{
                        $errors = $fileManager->checkUploadFile($_FILES['uploaded_file'], $_POST);
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
            $fileManager = FilesManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                if(isset($_POST['id_file_to_delete'])){
                    if(empty($fileManager->checkDeleteFile($_POST['id_file_to_delete']))){
                        $fileManager->deleteFile($_POST['id_file_to_delete']);
                        $allFiles = $fileManager->showFiles($_SESSION['user_id']);
                        echo $this->renderView('your_files.html.twig',
                                    ['user' => $user, 'allFiles' => $allFiles]);
                    }
                    else{
                        $errors = $fileManager->checkDeleteFile($_POST['id_file_to_delete']);
                        $allFiles = $fileManager->showFiles($_SESSION['user_id']);
                        echo $this->renderView('your_files.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFiles' => $allFiles]);                    
                    }
                }
                else{
                    $allFiles = $fileManager->showFiles($_SESSION['user_id']);
                    echo $this->renderView('your_files.html.twig',
                                ['user' => $user, 'allFiles' => $allFiles]);
                }
            }
            else{
                $allFiles = $fileManager->showFiles($_SESSION['user_id']);
                echo $this->renderView('your_files.html.twig',
                            ['user' => $user, 'allFiles' => $allFiles]);
            }
        }
        else{
            $this->redirect('login');
        } 
    }

}
