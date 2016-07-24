<?php
namespace Member;
use Core\Controller;

class CheckloginController extends Controller{
	public function index(){
		if ($this->uid && $this->username){
			$this->showAjaxReturn(array('status'=>'LOGINED'));
		}else {
			$this->showAjaxError(101, 'UNLOGINED', array('status'=>'UNLOGINED'));
		}
	}
}