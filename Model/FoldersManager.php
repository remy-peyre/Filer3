<?php

namespace Model;

class FoldersManager{
    
    private $DBManager;
    private $UserManager;
    private $FilesManager;
    
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new FoldersManager();
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->DBManager = DBManager::getInstance();
        $this->UserManager = UserManager::getInstance();
        $this->FilesManager = FilesManager::getInstance();
    }

    public function showFolders($user_id, $current_folder)
    {
        $data = $this->DBManager->findAllSecure("SELECT * FROM folders WHERE user_id = :user_id AND container_id = :current_folder ",
            ['user_id' => $user_id, 'current_folder' => $current_folder]);
        return $data;
    }

    public function checkCreateFolder($folder_name, $container)
    {
        $errors = array();

        return $errors;
    }

    public function createFolder($folder_name, $container)
    {
        $folder['foldername'] = $folder_name;
        if($container == 0){
            $folder['folderpath'] = 'uploads/' . $_SESSION['user_id'] . '/' . $folder_name;
            $folder['user_id'] = $_SESSION['user_id'];
            $folder['container_id'] = 0;
        }
        else{
            $container_folder_path = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE user_id = :user_id AND id = :folder_id", ['user_id' => $_SESSION['user_id'], 'folder_id' => $container]);
            $folder['folderpath'] = $container_folder_path['folderpath'] . '/' . $folder_name;
            $folder['user_id'] = $_SESSION['user_id'];
            $folder['container_id'] = $container;
        }     
        $this->DBManager->insert('folders', $folder);
        $data = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE user_id = :user_id AND foldername = :foldername", ['user_id' => $_SESSION['user_id'], 'foldername' => $folder['foldername']]);
        if($container == 0){
            $new_path = 'uploads/' . $_SESSION['user_id'] . '/' . $data['id'];
        }
        else{
            $new_path= $container_folder_path['folderpath'] . '/' . $data['id'];
        }  
       $update = $this->DBManager->findOneSecure("UPDATE folders SET folderpath = :folderpath where user_id = :user_id AND id = :folder_id", ['folderpath' => $new_path, 'user_id' => $_SESSION['user_id'], 'folder_id' => $data['id']]);
       mkdir($new_path);
    }

}