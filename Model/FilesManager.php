<?php

namespace Model;

class FilesManager{
    
    private $DBManager;
    private $UserManager;
    
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null)
            self::$instance = new FilesManager();
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->DBManager = DBManager::getInstance();
        $this->UserManager = UserManager::getInstance();
    }

    public function getFileByFilename($filename)
    {
        $data = $this->DBManager->findOneSecure("SELECT * FROM files WHERE filename = :filename AND user_id = :user_id",
                                ['filename' => $filename, 'user_id' => $_SESSION['user_id']]);
        return $data;
    }

    public function getFileById($file_id)
    {
        $data = $this->DBManager->findOneSecure("SELECT * FROM files WHERE id = :file_id AND user_id = :user_id",
                                ['file_id' => $file_id, 'user_id' => $_SESSION['user_id']]);
        return $data;
    }

    public function checkUploadFile($data, $post)
    {
        $errors = array();
        if(!empty($data['name'])){
            $type = dirname(mime_content_type($data['tmp_name']));
            $extensions = array('image', 'text', 'application', 'audio', 'video');
            if(in_array($type, $extensions) === false){
            $errors['extensions'] = 'this extension isn\'t allowed';
            }

            if(!empty($post['initial_new_name'])){
                $nameToTest = $post['initial_new_name'];
            }
            else{
                $nameToTest = $data['name'];
            }
            $data = $this->getFileByFilename($nameToTest);
            if($data){
                $errors['filename'] = "Name already used";
            }
        }
        else{
            $errors['fields'] = 'You have to select one file';
        }
        return $errors;
    }

    public function uploadFile($data, $post)
    {
        $type = dirname(mime_content_type($data['tmp_name']));
        if(!empty($post['initial_new_name'])){
            $file['filename'] = $post['initial_new_name'];
        }
        else{
            $file['filename'] = $data['name'];
        }
        $file['filepath'] =  'uploads/'. $_SESSION['user_id'] . '/' . $file['filename'];
        $file['user_id'] = $_SESSION['user_id'];
        $file['type'] = $type;
        $this->DBManager->insert('files', $file);
        $file = $this->DBManager->findOneSecure("SELECT * FROM files where filename = :filename", ['filename' => $file['filename']]);
        $new_path = 'uploads/'. $_SESSION['user_id'] . '/' . $file['id'] .  strrchr(basename($data['name']), '.');
        $update = $this->DBManager->findOneSecure("UPDATE `files` SET `filepath` = :newpath WHERE `id` =:file_id", ['file_id' => $file['id'], 'newpath' => $new_path]);
        move_uploaded_file($data["tmp_name"], $new_path);
    }

    public function showFiles($user_id)
    {
        $data = $this->DBManager->findAllSecure("SELECT * FROM files WHERE user_id = :user_id ",
            ['user_id' => $user_id]);
        return $data;
    }

    public function checkDeleteFile($file_id)
    {
        $errors = array();
        if(!empty($file_id)){
            $data = $this->getFileById($file_id);
            if(empty($data)){
                $errors['unknown_id'] = "We can't find this file"; 
            }
        }
        else{
            $errors['missing_id'] = "Can't find id please try again";
        }
        return $errors;
    }

    public function deleteFile($file_id)
    {
        $delete['file_id'] = $file_id;
        $data = $this->DBManager->findOneSecure("SELECT `filepath` FROM `files` WHERE `id` = :file_id", $delete);
        unlink($data['filepath']);
        $data = $this->DBManager->findOneSecure("DELETE  FROM files WHERE  `id` = :file_id", $delete);
        return true;
    }

    public function checkRenameFile($file_id, $new_name){
        $errors = array();
        if(!empty($file_id)){
            $data = $this->getFileById($file_id);
            if(empty($data)){
                $errors['unknown_id'] = "We can't find this file"; 
            }
            $test_name = $this->getFileByFilename($new_name);
            if(!empty($test_name)){
                $errors['name_already_used'] = "You already got one file with this name !";
            }
        }
        else{
            $errors['missing_id'] = "Can't find id please try again";
        }
        return $errors;
    }

    public function renameFIle($file_id, $new_name){
        $update = $this->DBManager->findOneSecure("UPDATE `files` SET `filename` = :newname WHERE `id` =:file_id", ['file_id' => $file_id, 'newname' => $new_name]);
    }
}