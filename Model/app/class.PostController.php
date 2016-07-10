<?php
namespace App;
class PostController extends BaseController{
	public function index(){
		
	}
	
	public function showlist(){
		global $G,$lang;
		$articlelist = post_get_page(0, $G['page']);
		include template('post_list');
	}
	
	public function showdetail(){
		global $G,$lang;
		$id = intval($_GET['id']);
		$article = post_get_data(array('id'=>$id));
		$content = post_get_content($id);
		if ($article['type'] == 'video'){
			$videodata = unserialize($content);
			$this->redirect($videodata['url']);
		}else {
			include template('post_detail');
		}
	}
}