<?php

namespace Controller;

use Model\UserManager;
use Model\FilesManager;
use Model\FoldersManager;

class FilesController extends BaseController
{

    public function uploadAction()
    {
        if (!empty($_SESSION['user_id'])){
            $manager = UserManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            $fileManager = FilesManager::getInstance();
            $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                if(!empty($_POST['upload_button'])){
                    if(empty($fileManager->checkUploadFile($_FILES['uploaded_file'], $_POST))){
                        $fileManager->uploadFile($_FILES['uploaded_file'], $_POST);
                        $this->redirect('your_files');
                    }
                    else{
                        $errors = $fileManager->checkUploadFile($_FILES['uploaded_file'], $_POST);
                        echo $this->renderView('upload.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFiles' => $allFiles]);
                    }
                }
                else if(!empty($_POST['replace_button'])){
                    if(empty($fileManager->checkReplaceFile($_POST['select_replace'], $_FILES['replacement_file']))){
                        $fileManager->replaceFile($_POST['select_replace'], $_FILES['replacement_file']);
                        $this->redirect('your_files');
                    }
                    else{
                        $errors = $fileManager->checkReplaceFile($_POST['select_replace'], $_FILES['replacement_file']);
                        echo $this->renderView('upload.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFiles' => $allFiles]);
                    }
                }
            }
            else{
                echo $this->renderView('upload.html.twig',
                                        ['user' => $user, 'allFiles' => $allFiles]);
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
            $folderManager = FoldersManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                if(isset($_POST['id_file_to_delete'])){
                    if(empty($fileManager->checkDeleteFile($_POST['id_file_to_delete']))){
                        $fileManager->deleteFile($_POST['id_file_to_delete']);
                        $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
                        $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
                        echo $this->renderView('your_files.html.twig',
                                    ['user' => $user, 'allFiles' => $allFiles, 'allFolders' => $allFolders]);
                    }
                    else{
                        $errors = $fileManager->checkDeleteFile($_POST['id_file_to_delete']);
                        $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
                        $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
                        echo $this->renderView('your_files.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFiles' => $allFiles, 'allFolders' => $allFolders]);                    
                    }
                }
                else if(isset($_POST['id_file_to_rename'])){
                    if(empty($fileManager->checkRenameFile($_POST['id_file_to_rename'], $_POST['new_name']))){
                        $fileManager->renameFile($_POST['id_file_to_rename'], $_POST['new_name']);
                        $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
                        $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
                        echo $this->renderView('your_files.html.twig',
                                    ['user' => $user, 'allFiles' => $allFiles, 'allFolders' => $allFolders]);
                    }
                    else{
                        $errors = $fileManager->checkRenameFile($_POST['id_file_to_rename'], $_POST['new_name']);
                        $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
                        $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
                        echo $this->renderView('your_files.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFiles' => $allFiles, 'allFolders' => $allFolders]);                    
                    }
                }
                else{
                    $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
                    $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
                    echo $this->renderView('your_files.html.twig',
                                ['user' => $user, 'allFiles' => $allFiles, 'allFolders' => $allFolders]);
                }
            }
            else{
                $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
                $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
                echo $this->renderView('your_files.html.twig',
                            ['user' => $user, 'allFiles' => $allFiles, 'allFolders' => $allFolders]);
            }
        }
        else{
            $this->redirect('login');
        } 
    }

    public function applicationAction()
    {
        if (!empty($_SESSION['user_id'])){
            $manager = UserManager::getInstance();
            $fileManager = FilesManager::getInstance();
            $folderManager = FoldersManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
            $allFilesApplication = $fileManager->showApplication($_SESSION['user_id'], $_SESSION['current_folder']);
            echo $this->renderView('application.html.twig',
                ['user' => $user, 'allFilesApplication' => $allFilesApplication, 'allFolders' => $allFolders]);
        }
        else
            $this->redirect('login');
    }

    public function pictureAction()
    {
        if (!empty($_SESSION['user_id'])){
            $manager = UserManager::getInstance();
            $fileManager = FilesManager::getInstance();
            $folderManager = FoldersManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
            $allFilesPicture = $fileManager->showPicture($_SESSION['user_id'], $_SESSION['current_folder']);
            echo $this->renderView('picture.html.twig',
                ['user' => $user, 'allFilesPicture' => $allFilesPicture, 'allFolders' => $allFolders]);
        }
        else
            $this->redirect('login');
    }

    public function audio_videoAction()
    {
        if (!empty($_SESSION['user_id'])){
            $manager = UserManager::getInstance();
            $fileManager = FilesManager::getInstance();
            $folderManager = FoldersManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
            $allFilesAudio = $fileManager->showAudio($_SESSION['user_id'], $_SESSION['current_folder']);
            $allFilesVideo = $fileManager->showVideo($_SESSION['user_id'], $_SESSION['current_folder']);
            echo $this->renderView('audio_video.html.twig',
                ['user' => $user, 'allFilesAudio' => $allFilesAudio, 'allFilesVideo' => $allFilesVideo, 'allFolders' => $allFolders]);
        }
        else
            $this->redirect('login');
    }
}
