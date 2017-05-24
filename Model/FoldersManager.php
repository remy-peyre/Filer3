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
            $container_folder_path = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE user_id = :user_id AND id = :folder_id", ['user_id' => $_SESSION['user_id'], 'id' => $container]);
            $folder['folderpath'] = $container_folder_path . '/' . $folder_name;
            $folder['user_id'] = $_SESSION['user_id'];
            $folder['container_id'] = $container;
        }     
        $this->DBManager->insert('folders', $folder);
    }

}