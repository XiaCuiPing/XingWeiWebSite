<?php
namespace Admin;
class GoodscatController extends CategoryController{
	function __construct(){
		parent::__construct();
		$this->type = 'goods';
	}
	
	public function merge(){
		global $G,$lang;
		if ($this->checkFormSubmit()) {
			$source = $_GET['source'];
			$target = $_GET['target'];
			if ($source && is_array($source) && $target) {
				$source = array_diff($source, array($target));
				$source = implodeids($source);
				goods_update_data(array('catid'=>array('IN', $source)), array('catid'=>$target));
				category_delete_data(array('catid'=>array('IN', $source)));
				category_update_cache($this->type);
				$this->showSuccess('update_succeed');
			}else {
				$this->showError('no_select');
			}
		}else {
			
			$sourceoptions = category_get_options(0, 0, 0, $this->type);
			$targetoptions = category_get_options(0, 0, 1, $this->type);
			include template('goods_merge');
		}
	}
}