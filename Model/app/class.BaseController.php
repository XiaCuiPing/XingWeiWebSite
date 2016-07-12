<?php
namespace App;
use Core\Controller;
class BaseController extends Controller{
	protected $platform;
	protected $appkey = '40a3e8e50fa27b8e6f1dd1a4b7454a0a';
	protected $isIOS = false;
	protected $isAndroid  = false;
	protected $longitude  = 0;
	protected $latitude   = 0;
	protected $version = '1.0';
	
	function __construct(){
		parent::__construct();
		$this->platform = trim($_GET['platform']);
		$this->isIOS = $this->platform == 'ios' ? true : false;
		$this->isAndroid = $this->platform == 'android' ? true : false;
		$this->longitude = isset($_GET['longitude']) ? $_GET['longitude'] : 0;
		$this->latitude  = isset($_GET['latitude']) ? $_GET['latitude'] : 0;
		$this->version = isset($_GET['version']) ? floatval($_GET['version']) : 1.0;
		
		$this->uid = intval($_GET['uid']);
		$this->username  = htmlspecialchars(trim($_GET['username']));
		
		if ($_GET['appkey'] != $this->appkey){
			$this->showAppError(201, 'APPKEY FAILD');
		}
	}
	
	/**
	 * 返回APP数据
	 * @param id $data
	 */
	public function showAppData($data){
		@header('Content-type: application/json');
		echo json_encode(array('errno'=>0,'error'=>'success','data'=>$data));
		exit();
	}
	
	/**
	 * 返回APP错误
	 * @param number $errno 错误代码
	 * @param string $error 错误信息
	 * @param string $data 相关数据
	 */
	public function showAppError($errno=0,$error='FAILD',$data=''){
		@header('Content-type: application/json');
		echo json_encode(array('errno'=>$errno,'error'=>$error,'data'=>$data));
		exit();
	}
}