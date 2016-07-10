<?php
namespace Common;
use Core\Controller;
class DistrictController extends Controller{
	public function index(){}
	
	public function getoption(){
		$fid = intval($_GET['fid']);
		$selected = trim($_GET['selected']);
		$options = '';
		foreach (district_get_list(array('fid'=>$fid), 0) as $dst){
			$s = $selected == $dst['name'] ? ' selected="selected"' : '';
			$options.= '<option idvalue="'.$dst['id'].'" value="'.$dst['name'].'"'.$s.'>'
					.$dst['name'].'</option>';
		}
		echo $options;
		exit();
	}
	
	public function getjson(){
		$fid = intval($_GET['fid']);
		$this->showAjaxReturn(district_get_list(array('fid'=>$fid)));
	}
}