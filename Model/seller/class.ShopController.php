<?php
namespace Seller;
class ShopController extends BaseController{
	public function index(){
		global $G, $lang;
		$shop = shop_get_data(array('uid'=>$this->uid));
		include template('my_shop');
	}
	
	public function create(){
		global $G,$lang;
		if ($this->checkFormSubmit()) {
			$shopnew = $_GET['shopnew'];
			if ($shopnew['shopname']) {
				if (shop_get_num(array('uid'=>$this->uid))) {
					$this->showError('');
				}else {
					$shopnew['uid'] = $this->uid;
					$shopnew['dateline']  = TIMESTAMP;
					$shopnew['longitude'] = floatval($shopnew['longitude']);
					$shopnew['latitude']  = floatval($shopnew['latitude']);
					shop_add_data($shopnew);
					$this->showSuccess('save_succeed', '', array(
							array('text'=>'back', 'url'=>'/?m='.$G['m'].'&c='.$G['c'])
					));
				}
			}else {
				$this->showError('undefined_action');
			}
		}else {
			
			$categoryoptions = category_get_options(0,0,0,'shop');
			$location = getLocationByIP();
			include template('my_shop_form');
		}
	}
	
	public function edit(){
		global $G,$lang;
		if ($this->checkFormSubmit()) {
			$shopnew = $_GET['shopnew'];
			if ($shopnew['shopname']) {
				$shopnew['longitude'] = floatval($shopnew['longitude']);
				$shopnew['latitude']  = floatval($shopnew['latitude']);
				shop_update_data(array('uid'=>$this->uid), $shopnew);
				$this->showSuccess('update_succeed', '', array(
						array('text'=>'back', 'url'=>'/?m='.$G['m'].'&c='.$G['c'])
				));
			}else {
				$this->showError('undefined_action');
			}
		}else {
			$shop = shop_get_data(array('uid'=>$this->uid));
			$categoryoptions = category_get_options(0,$shop['catid'],0,'shop');
			$location = array(
					'longitude'=>$shop['longitude'],
					'latitude'=>$shop['latitude']
			);
			include template('my_shop_form');
		}
	}
}