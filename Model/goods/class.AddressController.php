<?php
namespace Goods;
class AddressController extends BaseController{
	public function index(){
		
	}
	
	public function add(){
		$addressnew = $_GET['addressnew'];
		if ($addressnew['street'] && $addressnew['consignee'] && $addressnew['phone']){
			$addressnew['uid'] = $this->uid;
			address_add_data($addressnew);
			$this->showAjaxReturn(array('result'=>'SUCCEED'));
		}
	}
}