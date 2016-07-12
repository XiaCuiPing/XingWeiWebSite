<?php
namespace App;
class LoginController extends BaseController{
	public function index(){
		global $G,$lang;
		
		$account  = trim($_GET['account']);
		$password = trim($_GET['password']);
		
		if (ismobile($account)){
			$res = member_login($account, $password, 'mobile');
		}
		
		if (isemail($account)){
			$res = member_login($account, $password, 'email');
		}
		
		if (is_numeric($res)){
			$res = member_login($account, $password);
		}
		
		if (is_array($res)) {
			$this->showAppData($res);
		}else {
			switch ($res){
				case 1: $this->showAppError(301, 'ACCOUNT NOT EXIST');
				break;
				case 2: $this->showAppError(302, 'PASSWORD NOT MATCH');
				break;
				default:$this->showAppError(303, 'PARAMETER MISSING');
			}
		}
	}
}