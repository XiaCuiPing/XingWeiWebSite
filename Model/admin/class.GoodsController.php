<?php
namespace Admin;
class GoodsController extends BaseController{
	public function index(){
		$this->showlist();
	}
	
	/**
	 * 显示商品列表
	 */
	public function showlist(){
		global $G,$lang;
		if ($this->checkFormSubmit()) {
			$goodsids = $_GET['id'];
			if ($goodsids && is_array($goodsids)) {
				$goodsids = implodeids($goodsids);
				switch ($_GET['option']) {
					//删除
					case 'delete':
						$this->deleteGoods($goodsids);
						$this->showSuccess('delete_succeed');
						break;
					//上架
					case 'shelve':
						goods_update_data(array('id'=>array('IN', $goodsids)), array('status'=>0));
						$this->showSuccess('update_succeed');
						break;
					//下架
					case 'unshelve':
						goods_update_data(array('id'=>array('IN', $goodsids)), array('status'=>-1));
						$this->showSuccess('update_succeed');
						break;
					default:;
				}
			}else {
				$this->showError('no_select');
			}
		}else {
			$pagesize = 20;
			$condition = array();
			$catid = intval($_GET['catid']);
			if ($catid) $condition['catid'] = $catid;
			$keyword = trim($_GET['keyword']);
			if ($keyword) $condition['name'] = array('LIKE', $keyword);
			
			$totalnum = goods_get_num($condition);
			$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$goodslist = goods_get_page($condition, $G['page'], $pagesize, 'id DESC');
			if ($goodslist) {
				$goodsids = array_keys($goodslist);
				$piclist = goods_get_image_list($goodsids);
				foreach ($piclist as $pic) {
					$goodslist[$pic['dataid']]['thumb'] = $pic['thumb'];
					$goodslist[$pic['dataid']]['image'] = $pic['image'];
				}
				unset($piclist, $pic);
				
				$shopids = $comma = '';
				foreach ($goodslist as $goods) {
					$shopids.= $comma.$goods['shopid'];
					$comma = ',';
				}
				$shoplist = shop_get_list(array('shopid'=>array('IN', $shopids)), $pagesize);
			}
			$pages = $this->showPages($G['page'], $pagecount, $totalnum, "catid=$catid&keyword=$keyword", 1);
			$categoryoptions = category_get_options(0, $catid, 0, 'goods');
			include template('goods_list');
		}
	}
	
	/**
	 * 添加商品
	 */
	public function add(){
		global $G,$lang;
		if ($this->checkFormSubmit()){
			$goodsnew = $_GET['goodsnew'];
			if ($goodsnew['name'] && $goodsnew['price']) {
				$goodsnew['no'] = goods_create_no();
				$goodsnew['uid'] = $this->uid;
				$goodsnew['dateline'] = TIMESTAMP;
				$id = goods_add_data($goodsnew);
			
				$description = $_GET['description'];
				goods_add_desc(array('gid'=>$id, 'description'=>$description));
			
				$piclist = $_GET['piclist'];
				if ($piclist && is_array($piclist)) {
					foreach ($piclist as $pic) {
						image_add_data(array(
								'dataid'=>$id,
								'datatype'=>'goods',
								'image'=>$pic['image'],
								'thumb'=>$pic['thumb'],
								'isremote'=>0
						));
					}
				}
				$this->showSuccess('save_succeed');
			}
		}else {
			$shoplist = shop_get_list(0,10000);
			include template('goods_form');
		}
	}
	
	/**
	 * 编辑商品信息
	 */
	public function edit(){
		global $G,$lang;
		$id = intval($_GET['id']);
		$condition = array('id'=>$id);
		if ($this->checkFormSubmit()){
			$goodsnew = $_GET['goodsnew'];
			if ($goodsnew['name'] && $goodsnew['price']) {
				goods_update_data($condition, $goodsnew);
				$description = $_GET['description'];
				goods_add_desc(array('gid'=>$id, 'description'=>$description));
					
				$piclist = $_GET['piclist'];
				image_delete_data(array('dataid'=>$id, 'datatype'=>'goods'));
				if ($piclist && is_array($piclist)) {
					foreach ($piclist as $pic) {
						image_add_data(array(
								'dataid'=>$id,
								'datatype'=>'goods',
								'image'=>$pic['image'],
								'thumb'=>$pic['thumb'],
								'isremote'=>0
						));
					}
				}
				$this->showSuccess('update_succeed');
			}
		}else {
			$goods = goods_get_data($condition);
			$description = goods_get_desc($id);
			$piclist = image_get_list($id, 'goods');
			$shoplist = shop_get_list(0,10000);
			include template('goods_form');
		}
	}
	
	/**
	 * 删除商品
	 * @param mixed $goodids
	 */
	private function deleteGoods($goodids) {
		$goodids = is_array($goodids) ? implodeids($goodids) : $goodids;
		$res = goods_delete_data(array('id'=>array('IN', $goodids)));
		goods_delete_desc(array('gid'=>array('IN', $goodids)));
		image_delete_data(array('datatype'=>'goods', 'dataid'=>array('IN', $goodids)));
		comment_delete_data(array('datatype'=>'goods', 'dataid'=>array('IN', $goodids)));
		return $res;
	}
}