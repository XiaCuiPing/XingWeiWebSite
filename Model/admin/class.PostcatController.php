<?php
namespace Admin;
class PostcatController extends CategoryController{
	function __construct(){
		parent::__construct();
		$this->type = 'article';
	}
	
	public function delete($deleteids){
		if ($deleteids) {
			parent::delete($deleteids);
			post_delete_data(array('catid'=>array('IN', $deleteids)));
		}
	}
	
	public function merge(){
		if ($this->checkFormSubmit()){
			$source = $_GET['source'];
			$target = $_GET['target'];
			if ($source && is_array($source) && $target){
				$source = array_diff($source, array($target));
				if (!empty($source)) {
					post_update_data(array('catid'=>array('IN',implodeids($source))), array('catid'=>$target));
					category_delete_data(array('catid'=>array('IN', implodeids($source))));
					category_update_cache($this->type);
				}
				$this->showSuccess('update_succeed');
			}else {
				$this->showError('undefined_action');
			}
		}else {
			global $G,$lang;
			$categorylist = category_get_list($this->type);
			$categoryoptions = category_get_options(0,0,0,$this->type);
			include template('post_merge');
		}
	}
}