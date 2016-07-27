<?php
namespace Lianmeng;
class DetailController extends BaseController{
	public function index(){
		global $G,$lang;
		$id = intval($_GET['id']);
		$business = business_get_data(array('id'=>$id));
		$description = business_get_desc($id);
		include template('detail');
	}
}