<?php

namespace Controller;

use Model\UserManager;
use Model\FoldersManager;

class FoldersController extends BaseController
{
    public function folderAction(){
        if (!empty($_SESSION['user_id'])){
            $folderManager = FoldersManager::getInstance();
            $allFolders= $folderManager->showFolders($_SESSION['user_id'], $_SESSION['current_folder']);
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                if(!empty($_POST['folder_name'])){
                    if(empty($folderManager->checkCreateFolder($_POST['folder_name'], $_POST['wich_folder']))){
                        $folderManager->createFolder($_POST['folder_name'], $_POST['wich_folder']);
                        $this->redirect('your_files');
                    }
                    else{
                        $errors = $folderManager->checkCreateFolder($_POST['folder_name'], $_POST['wich_folder']);
                        echo $this->renderView('folder.html.twig',
                                    ['user' => $user, 'errors' => $errors, 'allFolders' => $allFolders]);
                    }                    
                }
                else{
                    echo $this->renderView('folder.html.twig', ['allFolders' => $allFolders]);
                }
            }
            else{
                echo $this->renderView('folder.html.twig', ['allFolders' => $allFolders]);
            }
        }
        else{
            $this->redirect('login');
        }
    }
}
