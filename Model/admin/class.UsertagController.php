<?php
namespace Admin;
class UsertagController extends BaseController{
	public function index(){
		global $G, $lang;
		if ($this->checkFormSubmit()) {
			$tags    = $_GET['tags'];
			$tagids  = implodeids($_GET['tagid']);
			$delete  = intval($_GET['delete']);
			$newtag  = $_GET['newtag'];
			if(!empty($tags) && is_array($tags)){
				foreach ($tags as $key=>$value){
					M('member_tag')->where(array('tagid'=>$key))->update(array('tag'=>$value));
				}
			}
			if($delete){
				if(!empty($tagids)){
					M('member_tag')->where("tagid IN($tagids)")->delete();
				}
			}
			if(!empty($newtag)){
				foreach ($newtag as $key=>$tag){
					if($tag) $this->t('member_tag')->insert(array('tag'=>$tag));
				}
			}
			$this->showSuccess('update_succeed');
		}else {
			$tags = array();
			$pagesize  = 30;
			$totalnum  = M('member_tag')->count();
			$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$taglist   = M('member_tag')->page($G['page'],$pagesize)->select();
			$pages = $this->showPages($G['page'], $pagecount, $totalnum);
			include template('usertag');
		}
	}
}