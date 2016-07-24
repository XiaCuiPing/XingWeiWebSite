<?php
namespace Member;
use Core\Controller;
class LoginController extends Controller{
	public function __construct(){
		parent::__construct();
		if ($this->uid && $this->username) $this->redirect('/?');
	}
	
	public function index(){
		if ($this->checkFormSubmit()){
			$this->chklogin();
		}else {
			member_show_login();
		}
	}
	
	/**
	 * 登录验证
	 */
	private function chklogin(){
		$account  = htmlspecialchars(trim($_GET['account_'.FORMHASH]));
		$password = trim($_GET['password_'.FORMHASH]);
		$captchacode = strtolower(trim($_GET['captchacode']));
		$this->checkCaptchacode($captchacode);
		
		if (isemail($account)){
			$returns = member_login($account, $password, 'email');
		}elseif (ismobile($account)){
			$returns = member_login($account, $password, 'mobile');
		}
		
		if (!is_array($returns) || !$returns['uid']) {
			$returns = member_login($account, $password, 'username');
		}
		
		if (is_array($returns) && $returns['uid']){
			$continue = $_GET['continue'] ? $_GET['continue'] : $_SERVER['HTTP_REFERER'];
			if ($continue !== curPageURL()){
				$this->redirect($continue);
			}else {
				$this->redirect('/?m=member');
			}
		}else {
			L('errmsg', $GLOBALS['lang']['member_login_error'][$returns]);
			$this->showError('errmsg');
		}
	}
	
	public function ajaxlogin(){
		if ($this->checkFormSubmit()){
			$account  = htmlspecialchars(trim($_GET['account_'.FORMHASH]));
			$password = trim($_GET['password_'.FORMHASH]);
			//$captchacode = strtolower(trim($_GET['captchacode']));
			//$this->checkCaptchacode($captchacode);
			
			if (isemail($account)){
				$returns = member_login($account, $password, 'email');
			}elseif (ismobile($account)){
				$returns = member_login($account, $password, 'mobile');
			}
			
			if (!is_array($returns) || !$returns['uid']) {
				$returns = member_login($account, $password, 'username');
			}
			
			if (is_array($returns) && $returns['uid']){
				$this->showAjaxReturn(array('result'=>'SUCCESS'));
			}else {
				$this->showAjaxError(101, 'PASSWORD ERROR', array('result'=>'FAILED'));
			}
		}else {
			member_show_ajax_login();
		}
	}
}