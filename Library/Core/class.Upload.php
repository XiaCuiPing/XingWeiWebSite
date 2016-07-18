<?php
namespace Core;
class Upload{
    public $allowtypes = array('zip','rar','7z','gz','doc','xls','ppt','pdf','jpg','jpeg','gif','png','mp4');
    public $maxsize = 10485760;
    public $errno = 0;
    private $file;
    
    public function __construct($inputname = 'filedata'){
        $this->file = &$_FILES[$inputname];
    }
    
    public function save($savename){
        @mkdir(dirname($savename),0777,true);
        $fileext = $this->getfileextension();
        if (!in_array($fileext, $this->allowtypes)){
        	$this->errno = 1;
        	return false;
        }
        if ($this->file['size'] > $this->maxsize){
        	$this->errno = 2;
        	return false;
        }
        if (!@is_uploaded_file($this->file['tmp_name'])){
        	$this->errno = 3;
        	return false;
        }
        
        if (!@move_uploaded_file($this->file['tmp_name'], $savename)){
        	$this->errno = 4;
        	return false;
        }else {
        	return true;
        }
    }
    
    /**
     * 设置文件名
     */
    public function setfilename(){
    	return date('YmdHis', time()).rand(100,999).rand(100,999).'.'.$this->getfileextension();
    }
    
    /**
     * 获取文件扩展名
     * @param string $file
     */
    public function getfileextension(){
    	$file = $this->file['name'];
    	return strtolower(str_replace(".", "", substr($file, strrpos( $file,'.'))));
    }
    
    public function oriname(){
    	return $this->file['name'];
    }
    public function type(){
    	return $this->file['type'];
    }
    public function size(){
    	return $this->file['size'];
    }
}