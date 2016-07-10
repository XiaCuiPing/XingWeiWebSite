<?php
namespace Goods;
class DetailController extends BaseController{
	public function index(){
		global $G,$lang;
		$id = intval($_GET['id']);
		$goods = goods_get_data(array('id'=>$id));
		$decription = goods_get_desc($id);
		$piclist = image_get_list($id, 'goods');
		$G['title'] = $goods['name'];
		include template('detail');
	}
}