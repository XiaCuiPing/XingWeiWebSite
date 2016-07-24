<?php
namespace Goods;
class CartController extends BaseController{
	function __construct(){
		parent::__construct();
		if (!$this->uid || !$this->username){
			$continue = urlencode(curPageURL());
			$this->redirect('/?m=member&c=login&continue='.$continue);
		}
	}
	
	public function index(){
		global $G,$lang;
		
		$cartlist = cart_get_page(array('uid'=>$this->uid), $G['page'], 20);
		if ($cartlist) {
			$goodsids = $comma = '';
			$numberlist = array();
			foreach ($cartlist as $cart){
				$goodsids.= $comma.$cart['goods_id'];
				$comma = ',';
				$numberlist[$cart['goods_id']] = $cart;
			}
			if ($goodsids) {
				$goodslist = goods_get_list(array('id'=>array('IN', $goodsids)), 100);
				if ($goodslist) {
					$datalist = array();
					foreach ($goodslist as $goods){
						$goods['number'] = $numberlist[$goods['id']]['goods_num'];
						$goods['total']  = $numberlist[$goods['id']]['goods_num'] * $goods['price'];
						$datalist[$goods['shopid']][$goods['id']] = $goods;
					}
					$cartlist = $datalist;
					
					$shopids = $cartlist ? implodeids(array_keys($cartlist)) : 0;
					$shoplist = shop_get_list(array('shopid'=>array('IN', $shopids)), 20);
				}
			}
			unset($numberlist, $goodsids, $cart, $goodslist, $goods, $datalist, $shopids);
		}
		//print_array($shoplist);exit();
		$G['title'] = '购物车';
		include template('cart_list');
	}
	
	public function add(){
		$id = intval($_GET['id']);
		$goods = goods_get_data(array('id'=>$id));
		if ($goods) {
			$cart = cart_get_data(array('goods_id'=>$id, 'uid'=>$this->uid));
			if ($cart) {
				cart_update_data(array('goods_id'=>$id, 'uid'=>$this->uid), 'goods_num=goods_num+1');
			}else {
				cart_add_data(array(
						'uid'=>$this->uid,
						'goods_id'=>$id,
						'goods_num'=>1
				));
			}
			$this->showAjaxReturn(array('result'=>'SUCCESS'));
		}else {
			$this->showAjaxError(101);
		}
	}
	
	public function save(){
		
	}
	
	public function delete(){
		
	}
	
}