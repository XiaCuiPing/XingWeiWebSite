<?php
namespace Member;
use Core\Controller;
class RegisterController extends Controller{
	function __construct(){
		parent::__construct();
		if ($this->uid) $this->redirect('/?m=home');
	}
	
	public function index(){
		if ($this->checkFormSubmit()){
			$this->save();
		}else {
			member_show_register();
		}
	}
	
	/**
	 * 保存注册信息
	 */
	function save(){
		global $lang;
		$username = htmlspecialchars(trim($_GET['username_'.FORMHASH]));
		$password = trim($_GET['password_'.FORMHASH]);
		$email    = trim($_GET['email_'.FORMHASH]);
		$captchacode = trim($_GET['captchacode']);
		$this->checkCaptchacode($captchacode);
		
		$data = array(
				'username'=>$username,
				'password'=>$password,
				'email'=>$email
		);
		
		$returns = member_register($data, 1);
		if (is_array($returns) && $returns['uid']) {
			$this->showSuccess('register_succeed','/?m=member',array(),'',true);
		}else {
			L('errmsg', $lang['member_register_error'][$returns]);
			$this->showError('errmsg');
		}
	}
	
	public function checkusername(){
		$username = htmlspecialchars($_GET['username']);
		if (member_get_num(array('username'=>$username))){
			$this->showAjaxError(-1);
		}else {
			$this->showAjaxReturn(1);
		}
	}
	
	public function checkemail(){
		$email = trim($_GET['email']);
		if (member_get_num(array('email'=>$email))){
			$this->showAjaxError(-1);
		}else {
			$this->showAjaxReturn(1);
		}
	}
}