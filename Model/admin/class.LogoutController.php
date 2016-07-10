<?php
namespace Admin;
class LogoutController extends BaseController{
	public function index(){
		cookie('adminlogined',null);
		$this->redirect('/?m=admin');
	}
}