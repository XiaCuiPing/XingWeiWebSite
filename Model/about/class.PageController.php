<?php
namespace About;
class PageController extends BaseController{
	public function detail(){
		global $G,$lang;
		
		$pageid = intval($_GET['pageid']);
		$pagedata = page_get_data(array('pageid'=>$pageid));
		
		$G['title'] = $pagedata['title'];
		include template('page_detail');
	}
}