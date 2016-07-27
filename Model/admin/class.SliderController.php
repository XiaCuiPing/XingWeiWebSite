<?php
namespace Admin;
class SliderController extends BaseController{
	public function index(){
		global $G,$lang;
		
		if ($this->checkFormSubmit()){
			//删除
			$delete = $_GET['delete'];
			if ($delete && is_array($delete)){
				slider_delete_data(array('id'=>array('IN', implodeids($delete))));
			}
			
			//更新
			$sliderlist = $_GET['sliderlist'];
			if ($sliderlist && is_array($sliderlist)){
				foreach ($sliderlist as $id=>$slider){
					slider_update_data(array('id'=>$id), $slider);
				}
			}
			
			//新增
			$slidernew = $_GET['slidernew'];
			if ($slidernew && is_array($slidernew)){
				foreach ($slidernew as $slider){
					slider_add_data($slider);
				}
			}
			
			$this->showSuccess('update_succeed');
		}else {
			$pagesize = 20;
			$totalnum = slider_get_num(0);
			$pagecount = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$sliderlist = slider_get_page(0, $G['page'], $pagesize);
			include template('slider_list');
		}
	}
	
	public function showimage(){
		global $G,$lang;
		$sliderid = intval($_GET['sliderid']);
		if ($this->checkFormSubmit()){
			$piclist = $_GET['piclist'];
			if ($piclist && is_array($piclist)) {
				$displayorder = 0;
				foreach ($piclist as $id=>$pic){
					$pic['displayorder'] = $displayorder;
					slider_update_image(array('id'=>$id), $pic);
					$displayorder++;
				}
			}
			$this->showSuccess('update_succeed');
		}else {
			$piclist = slider_get_image($sliderid);
			include template('slider_image');
		}
	}
	
	public function deleteimage(){
		$id = intval($_GET['id']);
		slider_delete_image(array('id'=>$id));
		$this->showAjaxReturn(array('result'=>'SUCCEESS'));
	}
	
	public function uploadimage(){
		global $G,$lang;
		$sliderid = intval($_GET['sliderid']);
		if($filedata = photo_upload_data()){
			slider_add_image(array(
					'sliderid'=>$sliderid,
					'image'=>$filedata['image']
			));
			$this->showAjaxReturn(array('image'=>$filedata['image']));
		}else {
			$this->showAjaxError(-1);
		}
	}
}