<?php
namespace Member;
class GoodsController extends BaseController{
	public function index(){
		$this->showlist();
	}
	
	public function showlist(){
		global $G,$lang;
		if ($this->checkFormSubmit()) {
			$goodsids = $_GET['id'];
			if ($goodsids && is_array($goodsids)) {
				$goodsids = implodeids($goodsids);
				switch ($_GET['option']) {
					case 'delete':
						$this->deleteGoods($goodsids);
						$this->showSuccess('delete_succeed');
						break;
						
					case 'shelve':
						goods_update_data(array('uid'=>$this->uid, 'id'=>array('IN', $goodsids)), array('status'=>0));
						$this->showSuccess('update_succeed');
						break;
					
					case 'unshelve':
						goods_update_data(array('uid'=>$this->uid, 'id'=>array('IN', $goodsids)), array('status'=>-1));
						$this->showSuccess('update_succeed');
						break;
					default:;
				}
				
			}else {
				$this->showError('no_select');
			}
		}else {
			$pagesize = 20;
			$totalnum = goods_get_num(array('uid'=>$this->uid));
			$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$goodslist = goods_get_page(array('uid'=>$this->uid), $G['page'], $pagesize, 'id DESC');
			if (!empty($goodslist)) {
				$piclist = goods_get_image_list(array_keys($goodslist));
				foreach ($piclist as $pic) {
					$goodslist[$pic['dataid']]['image'] = $pic['image'];
					$goodslist[$pic['dataid']]['thumb'] = $pic['thumb'];
					$goodslist[$pic['dataid']]['imageurl'] = $pic['imageurl'];
					$goodslist[$pic['dataid']]['thumburl'] = $pic['thumburl'];
				}
				unset($piclist,$pic);
			}
			include template('goods_list');
		}
	}
	
	public function publish(){
		global $G,$lang;
		if ($this->checkFormSubmit()) {
			$goodsnew = $_GET['goodsnew'];
			if ($goodsnew['name'] && $goodsnew['price']) {
				$shop = shop_get_data(array('uid'=>$this->uid));
				$goodsnew['shopid'] = $shop['shopid'];
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
			$categoryoptions = category_get_options(0, 0, 0, 'goods');
			include template('goods_form');
		}
	}
	
	public function edit(){
		global $G,$lang;
		$id = intval($_GET['id']);
		$condition = array('id'=>$id);
		if (!$G['member']['admincp']) $condition['uid'] = $this->uid;
		if ($this->checkFormSubmit()) {
			$goodsnew = $_GET['goodsnew'];
			if ($goodsnew['name'] && $goodsnew['price'] && goods_get_num($condition)) {
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
			$categoryoptions = category_get_options(0, $goods['catid'], 0, 'goods');
			include template('goods_form');
		}
	}
	
	private function deleteGoods($goodsids){
		$affect_rows = 0;
		$goodsids = is_array($goodsids) ? implodeids($goodsids) : $goodsids;
		foreach (goods_get_list(array('uid'=>$this->uid, 'id'=>array('IN', $goodsids))) as $goods){
			$affect_rows+= goods_delete_data(array('id'=>$goods['id']));
			goods_delete_desc(array('gid'=>$goods['id']));
			image_delete_data(array('datatype'=>'goods', 'dataid'=>$goods['id']));
			comment_delete_data(array('datatype'=>'goods', 'dataid'=>$goods['id']));
		}
		return $affect_rows;
	}
}