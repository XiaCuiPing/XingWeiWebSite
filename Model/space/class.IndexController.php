<?php
use Core\ViewController;
class IndexViewController extends ViewController{
	public $uid;
	public $member;
	public function init(){
		$uid = intval($_GET['uid']);
		$this->uid = $uid;
		$this->member = $this->t('member')->where(array('uid'=>$this->uid))->selectOne();
		switch ($GLOBALS['G']['ac']){
			case 'post':$this->post();
			break;
			case 'comment':$this->comment();
			break;
			case 'photo':$this->photo();
			break;
			default:$this->index();
		}
	}
	
	public function index(){
		global $G,$lang;
		$G['ac'] = 'index';
		$member  = $this->member;
		$G['title'] = $member['username'].$lang['s_home'];
		$profile = $this->t('member_profile')->where(array('uid'=>$this->uid))->selectOne();
		$tags = unserialize($profile['tags']);
		include template('space_index');
	}
	
	public function post(){
		global $G,$lang;
		$member = $this->member;
		$G['title'] = $member['username'].$lang['s_article'];
		$pagesize = 10;
		$totalnum = $this->t('post_title')->where(array('uid'=>$this->uid))->count();
		$pagecount = $totalnum<$pagesize ? 1 : ceil($totalnum/$pagesize);
		$start_limit = ($G['page']-1)*$pagesize;
		$articlelist = $this->t('post_title')->where(array('uid'=>$this->uid))->order('id','DESC')->page($G['page'],$pagesize)->select();
		$pages = $this->showPages($G['page'], $pagecount, $totalnum,"uid=".$this->uid);
		include template('space_post');
	}
	
	public function comment(){
		global $G,$lang;
		$member = $this->member;
		$G['title'] = $member['username'].$lang['s_comment'];
		$totalnum = $this->t('comment')->where(array('uid'=>$this->uid))->count();
		$commentlist = $this->t('comment')->where(array('uid'=>$this->uid))->limit(0,20)->order('cid','DESC')->select();
		include template('space_comment');
	}
	
	public function photo(){
		global $G,$lang;
		$member = $this->member;
		$G['title'] = $member['username'].$lang['s_photo'];
		$totalnum = $this->t('photo')->where(array('uid'=>$this->uid))->count();
		$piclist  = $this->t('photo')->where(array('uid'=>$this->uid))->limit(0,20)->order('photoid','DESC')->select();
		include template('space_photo');
	}
}