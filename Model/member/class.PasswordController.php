<?php
namespace Member;
class PasswordController extends BaseController{
	public function index(){
		if ($this->checkFormSubmit()){
			$password    = trim($_GET['password']);
			$newpassword = trim($_GET['newpassword']);
			if ($password && $newpassword){
				$account = member_get_data(array('uid'=>$this->uid));
				if ($account['password'] !== sha1(md5($password))){
					$this->showError('old_password_error');
				}else {
					member_update_data(array('uid'=>$this->uid), array('password'=>sha1(md5($newpassword))));
					$this->showSuccess('modi_succeed');
				}
			}else {
				$this->showError('new_password_error');
			}
		}else {
			global $G,$lang;
			include template('password');
		}
	}
}