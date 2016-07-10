<?php
namespace Admin;
class SettingController extends BaseController{
	public function index(){
		
	}
	
	private function getSettings(){
		$settings = array();
		$settinglist = M('setting')->field('skey,svalue')->select();
		foreach ($settinglist as $list){
			$val = unserialize($list['svalue']);
			if(is_array($val)){
				$list['svalue'] = $val;
			}
			$settings[$list['skey']] = $list['svalue'];
		}
		return $settings;
	}
	
	public function basic(){
		global $G,$lang;
		$setting = $this->getSettings();
		include template('setting_basic');
	}
	
	public function optimiz(){
		global $G,$lang;
		$setting = $this->getSettings();
		include template('setting_optimiz');
	}
	
	public function register(){
		global $G,$lang;
		$setting = $this->getSettings();
		include template('setting_register');
	}
	
	public function save(){
		if ($this->checkFormSubmit()){
			$settingnew = $_GET['settingnew'];
			foreach ($settingnew as $key=>$value){
				if(is_array($value)) $value = serialize($value);
				M('setting')->where("skey='$key'")->update(array('svalue'=>$value));
			}
			$this->updatecache();
			$this->showSuccess('modi_succeed');
		}	
	}
	
	public function updatecache(){
		$settings = $this->getSettings();
		cache('settings', $settings);
	}
}