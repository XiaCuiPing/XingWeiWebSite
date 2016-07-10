<?php
namespace Member;
class ProfileController extends BaseController{
	public function index(){
		if ($this->checkFormSubmit()){
			$accountnew = $_GET['accountnew'];
			$profilenew = $_GET['profilenew'];
			if (isemail($accountnew['email']) || ismobile($accountnew['mobile'])){
				member_update_data(array('uid'=>$this->uid), $accountnew);
			}
			
			$profilenew['locked'] = 1;
			$profilenew['modified'] = TIMESTAMP;
			member_update_profile($this->uid, $profilenew);
			$this->showSuccess('modi_succeed');
		}else {
			global $G,$lang;
			$account = member_get_data(array('uid'=>$this->uid));
			$profile = member_get_profile($this->uid);
			include template('profile');
		}
	}
}