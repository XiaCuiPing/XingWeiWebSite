<?php
namespace Admin;
class DistrictController extends BaseController{
	public function index(){
		if ($this->checkFormSubmit()){
			$delete = $_GET['delete'];
			if ($delete && is_array($delete)) {
				$deleteids = implodeids($delete);
				district_delete_data(array('id'=>array('IN', $deleteids)));
			}
			
			$districtnew = $_GET['districtnew'];
			if ($districtnew && is_array($districtnew)) {
				foreach ($districtnew as $id=>$district) {
					if ($district['name']) {
						district_update_data(array('id'=>$id), $district);
					}
				}
			}
			
			$newdistrict = $_GET['newdistrict'];
			if ($newdistrict && is_array($newdistrict)) {
				foreach ($newdistrict as $district){
					if ($district['name']) {
						district_add_data($district);
					}
				}
			}
			
			$this->showSuccess('update_succeed');
		}else {
			global $G;
			$province = intval($_GET['province']);
			$city     = intval($_GET['city']);
			$county   = intval($_GET['county']);
			
			$fid = 0;
			$level = 1;
			$districtlist = $provincelist = $citylist = $countylist = array();
			$provincelist = district_get_list(array('fid'=>0), 0);
			$districtlist = $provincelist;
			if($province){
				$level = 2;
				$fid = $province;
				$citylist = district_get_list(array('fid'=>$province), 0);
				$districtlist = $citylist;
			}
			
			if($city){
				$level = 3;
				$fid = $city;
				$countylist = district_get_list(array('fid'=>$city), 0);
				$districtlist = $countylist;
			}
			if($county){
				$level = 4;
				$fid = $county;
				$districtlist = district_get_list(array('fid'=>$county), 0);
			}
			include template('district');
		}
	}
}