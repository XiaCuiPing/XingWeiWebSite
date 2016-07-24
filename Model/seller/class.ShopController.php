<?php
namespace Seller;
class ShopController extends BaseController{
	public function index(){
		$shop = shop_get_data(array('uid'=>$this->uid));
		if ($shop) {
			$this->setting();
		}else {
			global $G,$lang;
			$G['title'] = '店铺设置';
			include template('shop_empty');
		}
	}
	
	public function setting(){
		global $G, $lang;
		$shop = shop_get_data(array('uid'=>$this->uid));
		if ($this->checkFormSubmit()){
			$shopnew = $_GET['shopnew'];
			if ($shopnew['shopname']) {
				if ($filedata = photo_upload_data()){
					$shopnew['logo'] = $filedata['image'];
				}
				if ($shop) {
					$shopid = $shop['shopid'];
					shop_update_data(array('uid'=>$this->uid), $shopnew);
				}else {
					$shopnew['uid'] = $this->uid;
					$shopnew['dateline'] = TIMESTAMP;
					$shopid = shop_add_data($shopnew);
				}
				$description = $_GET['description'];
				shop_add_desc(array('uid'=>$this->uid,'shopid'=>$shopid, 'description'=>$description));
				$this->showSuccess('save_succeed', '', array(
						array('text'=>'back', 'url'=>'/?m='.$G['m'].'&c='.$G['c'])
				));
			}else {
				$this->showError('undefined_action');
			}
		}else {
			$description = shop_get_desc(array('uid'=>$this->uid, 'shopid'=>$shop['shopid']));
			$categoryoptions = category_get_options(0, 0, 0, 'shop');
			include template('shop_setting');
		}
	}
	
	public function mark(){
		global $G,$lang;
		$shop = shop_get_data(array('uid'=>$this->uid));
		if ($this->checkFormSubmit()) {
			$shopnew = $_GET['shopnew'];
			shop_update_data(array('uid'=>$this->uid), $shopnew);
			$this->showSuccess('save_succeed');
		}else {
			$G['title'] = '位置标注';
			include template('shop_mark');
		}
	}
}