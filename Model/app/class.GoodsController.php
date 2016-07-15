<?php
namespace App;
class GoodsController extends BaseController{
	public function index(){
		
	}
	
	/**
	 * 获取商品详细数据
	 */
	public function getdata(){
		$id = intval($_GET['id']);
		$goods = goods_get_data(array('id'=>$id));
		if ($goods){
			$image = image_get_data(array('datatype'=>'goods', 'dataid'=>$id));
			$goods['image'] = image($image['image']);
			$goods['thumb'] = image($image['thumb']);
			
			$this->showAppData($goods);
		}else {
			$this->showAppError(404, 'GOODS NOT FOUND');
		}
	}
	
	/**
	 * 获取商品列表
	 */
	public function getlist(){
		$catid = intval($_GET['catid']);
		$num = isset($_GET['num']) ? intval($_GET['num']) : 10;
		$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
		
		$condition = $catid ? array('catid'=>$catid) : '';
		$goodslist = goods_get_local_list($condition, $this->longitude, $this->latitude, $num, $limit);
		$this->showAppData(array_values($goodslist));
	}
	
	/**
	 * 获取商品分页列表
	 */
	public function getpage(){
		$catid = intval($_GET['catid']);
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 20;
		$condition = $catid ? array('catid'=>$catid) : '';
		
		$goodslist = goods_get_local_page($condition, $this->longitude, $this->latitude, G('page'), $pagesize);
		$this->showAppData(array_values($goodslist));
	}
	
	/**
	 * 显示商品详细页
	 */
	public function showdetail(){
		global $G,$lang;
		
		$id = intval($_GET['id']);
		$goods = goods_get_data(array('id'=>$id));
		$goods['piclist'] = image_get_list($id, 'goods');
		
		$description = goods_get_desc($id);
		
		$shop = shop_get_data(array('shopid'=>$goods['shopid']));
		include template('goods_detail');
	}
	
	public function showlist(){
		
	}
	
	public function showpage(){
		global $G,$lang;
		$catid = intval($_GET['catid']);
		$pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 20;
		$condition = $catid ? array('catid'=>$catid) : '';
		
		$goodslist = goods_get_local_page($condition, $this->longitude, $this->latitude, G('page'), $pagesize);
		include template('goods_page');
	}
}