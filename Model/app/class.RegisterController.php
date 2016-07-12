<?php
namespace App;
class RegisterController extends BaseController{
	public function index(){
		global $G;
		
		$username = htmlspecialchars($_GET['username']);
		$mobile   = trim($_GET['mobile']);
		$password = trim($_GET['password']);
		$res = member_register(array(
				'username'=>$username,
				'mobile'=>$mobile,
				'password'=>$password
		), 1);
		
		if (is_array($res)) {
			$this->showAppData($res);
		}else {
			switch ($res){
				case 1:$this->showAppError(301, 'USERNAME EXIST');
				break;
		
				case 2:$this->showAppError(302, 'MOBILE EXIST');
				break;
		
				case 3:$this->showAppError(303, 'EMAIL EXIST');
				break;
				
				case 4:$this->showAppError(304, '');
				break;
				
				case 5:$this->showAppError(305, 'PASSWORD FAILD');
				break;
				
				default:$this->showAppError(306, 'PARAMETER MISSING');
			}
		}
	}
}