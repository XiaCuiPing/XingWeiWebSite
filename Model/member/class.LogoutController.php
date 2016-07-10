<?php
namespace Member;
use Core\Controller;
class LogoutController extends Controller{	
	public function index(){
		cookie(null);
		$contiue = trim($_GET['continue']);
		$contiue = $contiue ? $contiue : $_SERVER['HTTP_REFERER'];
		if ($contiue !== curPageURL()){
			$this->redirect($contiue);
		}else {
			$this->redirect('/?m=login');
		}
	}
}