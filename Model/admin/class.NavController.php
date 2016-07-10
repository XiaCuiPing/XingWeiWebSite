<?php
namespace Admin;
class NavController extends BaseController{
	public function index(){
		if ($this->checkFormSubmit()){
			$this->save();
		}else {
			global $G,$lang;
			$navlist = M('nav')->order('displayorder ASC,nid ASC')->select();
			if ($navlist){
				$newlist = array();
				foreach ($navlist as $list){
					$newlist[$list['position']][$list['nid']] = $list;
				}
				$navlist = $newlist;
				unset($newlist);
			}

			$categoryoptions = category_get_options(0,0,0,'article');
			include template('nav');
		}
	}
	
	public function addcat(){
		$cids  = $_GET['cids'];
		$index = intval($_GET['index']);
		if(is_array($cids) && !empty($cids)){
			$cids = implode(',', $cids);
			$categorylist = $this->t('category')->where("catid IN($cids)")->select();
			foreach ($categorylist as $list){
				$index++;
				$data = array(
						'title'=>$list['cname'],
						'position'=>'top',
						'url'=>'/?m=post&c=list&catid='.$list['catid'],
						'displayorder'=>$index
				);
				$this->t('nav')->insert($data);
			}
		}
		$this->updatecache();
		$this->showAjaxReturn(array('state'=>1));
	}

	public function delete(){
		$nid = intval($_GET['nid']);
		$this->t('nav')->where(array('nid'=>$nid))->delete();
		$this->updatecache();
		$this->showAjaxReturn(array('state'=>1));
	}
	
	public function save(){
		$newnav = $_GET['newnav'];
		$navnew = $_GET['navnew'];
		if(is_array($navnew) && !empty($navnew)){
			foreach ($navnew as $nid=>$nav){
				if($nav['title']){
					$nav['available'] = $nav['available'] ? 1 : 0;
					$this->t('nav')->where("nid='$nid'")->update($nav);
				}
			}
		}
		if (is_array($newnav) && !empty($newnav)) {
			foreach ($newnav as $k=>$nav){
				if ($nav['title'] && $nav['url']) {
					$nav['available'] = $nav['available'] ? 1 : 0;
					$this->t('nav')->insert($nav);
				}
			}
		}
		$this->updatecache();
		$this->showSuccess('save_succeed');
	}
	
	public function updatecache(){
		$array = array('top'=>array(),'bottom'=>array(),'mobile'=>array());
		$navlist  = $this->t('nav')->where(array('available'=>1))->select();
		if ($navlist){
			foreach ($navlist as $nav){
				if ($nav['position'] == 'top'){
					array_push($array['top'], $nav);
				}
				if ($nav['position'] == 'bottom'){
					array_push($array['bottom'], $nav);
				}
				if ($nav['position'] == 'mobile'){
					array_push($array['mobile'], $nav);
				}
			}
		}else {
			$navlist = array();
		}
		cache('nav',$array);
	}
}