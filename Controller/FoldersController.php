<?php

namespace Controller;

use Model\UserManager;
use Model\FilesManager;
use Model\FoldersManager;

class FoldersController extends BaseController
{
    public function folderAction()
    {
        if (!empty($_SESSION['user_id'])){
            $manager = UserManager::getInstance();
            $folderManager = FoldersManager::getInstance();
            $allFolders= $folderManager->showAllFolders($_SESSION['user_id'], $_SESSION['current_folder']);
            $user = $manager->getUserById($_SESSION['user_id']);
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                if(!empty($_POST['folder_name'])){
                    if(empty($folderManager->checkCreateFolder($_POST['folder_name'], $_POST['wich_folder']))){
                        $folderManager->createFolder($_POST['folder_name'], $_POST['wich_folder']);
                        $this->redirect('yourFiles');
                    }
                    else{
                        $errors = $folderManager->checkCreateFolder($_POST['folder_name'], $_POST['wich_folder']);
                        echo $this->renderView('folder.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFolders' => $allFolders]);
                    }                    
                }
                else if(!empty($_POST['id_folder_to_rename'])){
                    if(empty($folderManager->checkRenameFolder($_POST['id_folder_to_rename'], $_POST['new_folder_name']))){
                        $folderManager->renameFolder($_POST['id_folder_to_rename'], $_POST['new_folder_name']);
                        $this->redirect('yourFiles');
                    }
                    else{
                        $errors = $folderManager->checkRenameFolder($_POST['id_folder_to_rename'], $_POST['new_folder_name']);
                        echo $this->renderView('folder.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFolders' => $allFolders]);
                    }                       
                }
                else if(!empty($_POST['id_folder_to_delete'])){
                    if(empty($folderManager->checkDeleteFolder($_POST['id_folder_to_delete']))){
                        $folderManager->deleteFolder($_POST['id_folder_to_delete']);
                        $this->redirect('yourFiles');
                    }
                    else{
                        $errors = $folderManager->checkDeleteFolder($_POST['id_folder_to_delete']);
                        echo $this->renderView('folder.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFolders' => $allFolders]);
                    }                       
                }
                else if(!empty($_POST['folder_to_move'])){
                    if(empty($folderManager->checkMoveFolder($_POST['folder_to_move'], $_POST['folder_destination']))){
                        $folderManager->moveFolder($_POST['folder_to_move'], $_POST['folder_destination']);
                        $this->redirect('yourFiles');
                    }
                    else{
                        $errors = $folderManager->checkMoveFolder($_POST['folder_to_move'], $_POST['folder_destination']);
                        echo $this->renderView('folder.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFolders' => $allFolders]);
                    }                     
                }
                else{
                    echo $this->renderView('folder.html.twig',
                                ['user' => $user, 'allFolders' => $allFolders]);

                }
            }
            else{
                echo $this->renderView('folder.html.twig',
                            ['user' => $user, 'allFolders' => $allFolders]);
            }
        }
        else{
            $this->redirect('login');
        }
    }

    public function switchCurrentFolderAction()
    {
        if(!empty($_SESSION['user_id'])){
            $manager = UserManager::getInstance();
            $filesManager = FilesManager::getInstance();
            $folderManager = FoldersManager::getInstance();
            $user = $manager->getUserById($_SESSION['user_id']);
            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                if(isset($_POST['new_current_folder'])){
                    if(empty($folderManager->checkSwitchCurrentFolder($_POST['new_current_folder'], $_SESSION['user_id']))){
                        $folderManager->switchCurrentFolder($_POST['new_current_folder']);
                    }
                    else{
                        $errors = $folderManager->checkSwitchCurrentFolder($_POST['new_current_folder'], $_SESSION['user_id']);
                        $allFolders = $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
                        $allFiles = $filesManager->showFiles($_SESSION['user_id'], $_SESSION['current_folder']);
                        echo $this->renderView('yourFiles.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFiles' => $allFiles, 'allFolders' => $allFolders]);
                    }
                }
            }
            $this->redirect('yourFiles');
        }
        else{
            $this->redirect('login');
        }
    }

}
