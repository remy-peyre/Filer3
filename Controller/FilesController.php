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
            $allFiles = $fileManager->showAllFiles($_SESSION['user_id'], $_SESSION['current_folder']);
            $folderManager = FoldersManager::getInstance();
            $allFolders = $folderManager->showAllFolders($_SESSION['user_id']);
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                if(!empty($_POST['upload_button'])){
                    if(empty($fileManager->checkUploadFile($_FILES['uploaded_file'], $_POST))){
                        $fileManager->uploadFile($_FILES['uploaded_file'], $_POST);
                        $this->redirect('yourFiles');
                    }
                    else{
                        $errors = $fileManager->checkUploadFile($_FILES['uploaded_file'], $_POST);
                        echo $this->renderView('upload.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFiles' => $allFiles, 'allFolders' => $allFolders]);
                    }
                }
                else if(!empty($_POST['replace_button'])){
                    if(empty($fileManager->checkReplaceFile($_POST['select_replace'], $_FILES['replacement_file']))){
                        $fileManager->replaceFile($_POST['select_replace'], $_FILES['replacement_file']);
                        $this->redirect('yourFiles');
                    }
                    else{
                        $errors = $fileManager->checkReplaceFile($_POST['select_replace'], $_FILES['replacement_file']);
                        echo $this->renderView('upload.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFiles' => $allFiles, 'allFolders' => $allFolders]);
                    }
                }
                else if(!empty($_POST['file_to_move'])){
                    if(empty($fileManager->checkMoveFile($_POST['file_to_move'], $_POST['folder_who_recept']))){
                        $fileManager->moveFile($_POST['file_to_move'], $_POST['folder_who_recept']);
                        $this->redirect('yourFiles');
                    }
                    else{
                        $errors = $fileManager->checkMoveFile($_POST['file_to_move'], $_POST['folder_who_recept']);
                        echo $this->renderView('upload.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFiles' => $allFiles, 'allFolders' => $allFolders]);
                    }             
                }
            }
            else{
                echo $this->renderView('upload.html.twig',
                                        ['user' => $user, 'allFiles' => $allFiles, 'allFolders' => $allFolders]);
            }
        }
        else{
            $this->redirect('login');
        }  
    }

    public function yourFilesAction()
    {
        if (!empty($_SESSION['user_id'])){
            $manager = UserManager::getInstance();
            $fileManager = FilesManager::getInstance();
            $folderManager = FoldersManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            $currentPath = $folderManager->giveCurrentPath($_SESSION['current_folder']);
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                if(isset($_POST['id_file_to_delete'])){
                    if(empty($fileManager->checkDeleteFile($_POST['id_file_to_delete']))){
                        $fileManager->deleteFile($_POST['id_file_to_delete']);
                        $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
                        $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
                        echo $this->renderView('yourFiles.html.twig',
                                    ['user' => $user, 'allFiles' => $allFiles, 'allFolders' => $allFolders, 'currentPath' => $currentPath]);
                    }
                    else{
                        $errors = $fileManager->checkDeleteFile($_POST['id_file_to_delete']);
                        $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
                        $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
                        echo $this->renderView('yourFiles.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFiles' => $allFiles, 'allFolders' => $allFolders, 'currentPath' => $currentPath]);                    
                    }
                }
                else if(isset($_POST['id_file_to_rename'])){
                    if(empty($fileManager->checkRenameFile($_POST['id_file_to_rename'], $_POST['new_name']))){
                        $fileManager->renameFile($_POST['id_file_to_rename'], $_POST['new_name']);
                        $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
                        $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
                        echo $this->renderView('yourFiles.html.twig',
                                    ['user' => $user, 'allFiles' => $allFiles, 'allFolders' => $allFolders, 'currentPath' => $currentPath]);
                    }
                    else{
                        $errors = $fileManager->checkRenameFile($_POST['id_file_to_rename'], $_POST['new_name']);
                        $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
                        $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
                        echo $this->renderView('yourFiles.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFiles' => $allFiles, 'allFolders' => $allFolders, 'currentPath' => $currentPath]);                    
                    }
                }
                else{
                    $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
                    $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
                    echo $this->renderView('yourFiles.html.twig',
                                ['user' => $user, 'allFiles' => $allFiles, 'allFolders' => $allFolders, 'currentPath' => $currentPath]);
                }
            }
            else{
                $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
                $allFiles = $fileManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
                echo $this->renderView('yourFiles.html.twig',
                            ['user' => $user, 'allFiles' => $allFiles, 'allFolders' => $allFolders, 'currentPath' => $currentPath]);
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
                ['user' => $user, 'allFilesApplication' => $allFilesApplication, 'allFolders' => $allFolders, 'currentPath' => $currentPath]);
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
                ['user' => $user, 'allFilesPicture' => $allFilesPicture, 'allFolders' => $allFolders, 'currentPath' => $currentPath]);
        }
        else
            $this->redirect('login');
    }

    public function audioVideoAction()
    {
        if (!empty($_SESSION['user_id'])){
            $manager = UserManager::getInstance();
            $fileManager = FilesManager::getInstance();
            $folderManager = FoldersManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
            $allFilesAudio = $fileManager->showAudio($_SESSION['user_id'], $_SESSION['current_folder']);
            $allFilesVideo = $fileManager->showVideo($_SESSION['user_id'], $_SESSION['current_folder']);
            echo $this->renderView('audioVideo.html.twig',
                ['user' => $user, 'allFilesAudio' => $allFilesAudio, 'allFilesVideo' => $allFilesVideo, 'allFolders' => $allFolders, 'currentPath' => $currentPath]);
        }
        else
            $this->redirect('login');
    }
}
