<?php
namespace Admin;
class SlideController extends BaseController {
	public function index(){
		if ($this->checkFormSubmit()){
			$delete = $_GET['delete'];
			if (!empty($delete) && is_array($delete)){
				$deleteids = implode(',', $delete);
				$this->t('slide')->where("slideid IN($deleteids)")->delete();
			}
			$newslide = $_GET['newslide'];
			if (!empty($newslide) && is_array($newslide)){
				foreach ($newslide as $slidename){
					if ($slidename) $this->t('slide')->insert(array('slidename'=>$slidename));
				}
			}
			
			$slidelist = $_GET['slidelist'];
			if (!empty($slidelist) && is_array($slidelist)){
				foreach ($slidelist as $slideid=>$slide){
					$this->t('slide')->where(array('slideid'=>$slideid))->update($slide);
				}
			}
			$this->updatecache();
			$this->showSuccess('update_succeed');
		}else {
			global $G,$lang;
			$slidelist = $this->t('slide')->select();
			include template('slide');
		}
	}
	
	public function edit(){
		if ($this->checkFormSubmit()){
			$slidedata = $_GET['slidedata'];
			$data = array();
			if (!empty($slidedata) && is_array($slidedata)){
				foreach ($slidedata['image'] as $key=>$image){
					$data[] = array(
							'image'=>$image,
							'title'=>$slidedata['title'][$key],
							'url'=>$slidedata['url'][$key],
							'summary'=>$slidedata['summary'][$key]
					);
				}	
			}
			$this->t('slide')->where(array('slideid'=>intval($_GET['slideid'])))->update(array('slidedata'=>serialize($data)));
			$this->updatecache();
			$this->showSuccess('save_succeed');
		}else {
			global $G, $lang;
			$slide = $this->t('slide')->where(array('slideid'=>intval($_GET['slideid'])))->selectOne();
			$slide['slidedata'] = $slide['slidedata'] ? unserialize($slide['slidedata']) : array();
			$slidedata = array();
			if ($slide['slidedata']){
				foreach ($slide['slidedata'] as $list){
					$list['imageurl'] = $list['image'] ? C('attachurl').$list['image'] : '';
					$slidedata[] = $list;
				}
			}
			include template('slide');
		}
	}
	
	public function upload(){
		$upload = new UploadImage();
		if ($filedata = $upload->saveImage()){
			$this->showAjaxReturn(array(
					'pic'=>$filedata['attachment'],
					'picurl'=>C('attachurl').$filedata['attachment']
			));
		}else {
			$this->showAjaxError(-1,$upload->error);
		}
	}
	
	private function updatecache(){
		$slidelist = $this->t('slide')->select();
		if ($slidelist){
			$newlist = array();
			foreach ($slidelist as $list){
				$datalist = array();
				if ($list['slidedata']){
					$slidedata = unserialize($list['slidedata']);
					foreach ($slidedata as $data){
						$data['image'] = $data['image'] ? C('attachurl').$data['image'] : '';
						$datalist[] = $data;
					}
				}
				$list['slidedata'] = $datalist;
				$newlist[$list['slideid']] = $list;
			}
			$slidelist = $newlist;
		}
		return cache('slides', $slidelist);
	}
}