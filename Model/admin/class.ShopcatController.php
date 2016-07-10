<?php
namespace Admin;
class ShopcatController extends CategoryController{
	function __construct(){
		parent::__construct();
		$this->type = 'shop';
	}
	
	public function delete($deleteids){
		if ($deleteids) {
			parent::delete($deleteids);
			
		}
	}
}