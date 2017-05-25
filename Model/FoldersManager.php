<?php

namespace Model;

class FoldersManager{
    
    private $DBManager;
    private $UserManager;
    
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
    }

    public function getFolderById($folder_id)
    {
        $data = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE id = :folder_id AND user_id = :user_id",
                                ['folder_id' => $folder_id, 'user_id' => $_SESSION['user_id']]);
        return $data;
    }

    public function showFolders($user_id, $current_folder)
    {
        $data = $this->DBManager->findAllSecure("SELECT * FROM folders WHERE user_id = :user_id AND container_id = :current_folder ",
            ['user_id' => $user_id, 'current_folder' => $current_folder]);
        return $data;
    }

    public function showAllFolders($user_id)
    {
        $data = $this->DBManager->findAllSecure("SELECT * FROM folders WHERE user_id = :user_id",
                                                ['user_id' => $user_id]);
        return $data;       
    }

    public function checkCreateFolder($folder_name, $container)
    {
        $errors = array();
        $query = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE foldername = :foldername AND user_id = :user_id AND container_id = :container_id", ['user_id' => $_SESSION['user_id'], 'foldername' => $folder_name, 'container_id' => $container]);
        if(!empty($query)){
            $errors['foldername'] = "You already got one folder with this name is this directory";
        }
        if($container != 0){
            $data = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE id = :id AND user_id = :user_id", ['user_id' => $_SESSION['user_id'], 'id' => $container]);
            if(empty($data)){
                $errors['folder_container'] = "We can't find this folder";
            }
        }
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

        $text = " Username " . $_SESSION['user_id'] . " has create a folder with success ! ";
        $this->UserManager->watchActionLog("access.log", $text);
    }

    public function checkRenameFolder($folder_id, $newname)
    {
        $errors = array();
        return $errors;
    }

    public function renameFolder($folder_id, $newname)
    {
        $update = $this->DBManager->findOneSecure("UPDATE `folders` SET `foldername` = :newname WHERE `id` =:folder_id", ['folder_id' => $folder_id, 'newname' => $newname]);

        $text = " Username " . $_SESSION['user_id'] . " has rename a folder with success ! ";
        $this->UserManager->watchActionLog("access.log", $text);
    }

    public function checkSwitchCurrentFolder($new_folder_id, $user_id)
    {
        $errors = array();
        return $errors;
    }

    public function switchCurrentFolder($new_folder_id)
    {
        $_SESSION['current_folder'] = $new_folder_id;

        $text = " Username " . $_SESSION['user_id'] . " has switch a folder with success ! ";
        $this->UserManager->watchActionLog("access.log", $text);
    }

    public function giveCurrentPath($folder_id, $current_path = "")
    {
        if($folder_id == 0){
            $current_path = $current_path . "Dossier Principal";
            $path = explode('/', $current_path);
            $path_to_return = "";
            for($i = count($path) -1; $i >= 0; $i-- ){
                $path_to_return = $path_to_return . $path[$i] . '/';
            }
            return $path_to_return;
        }
        else{
            $folder = $this->DBManager->findOneSecure("SELECT * FROM folders WHERE user_id = :user_id AND id = :folder_id", ['user_id' => $_SESSION['user_id'], 'folder_id' => $folder_id]);
            $current_path = $current_path . $folder['foldername'] . '/';
            $parent_name = $this->giveCurrentPath($folder['container_id'], $current_path);
            return $parent_name;
        }
    }

}