<?php
namespace Goods;
class BuyController extends BaseController{
	function __construct(){
		parent::__construct();
		if (!$this->uid || !$this->username){
			$continue = urlencode(curPageURL());
			$this->redirect('/?m=member&c=login&continue='.$continue);
		}
	}
	
	public function index(){
		global $G,$lang;
		
		$id = intval($_GET['id']);
		$goods = goods_get_data(array('id'=>$id));
		
		include template('buy');
	}
}