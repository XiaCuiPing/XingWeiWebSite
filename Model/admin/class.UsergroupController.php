<?php
namespace Admin;
class UsergroupController extends BaseController{
	public function index(){
		global $G,$lang;
		if($this->checkFormSubmit()){
			$delete = $_GET['delete'];
			if ($delete && is_array($delete)) {
				$deleteids = implodeids($delete);
				usergroup_delete_data(array('gid'=>array('IN', $deleteids), 'type'=>'custom'));
			}
			
			$grouplist = $_GET['grouplist'];
			if ($grouplist && is_array($grouplist)) {
				foreach ($grouplist as $gid=>$group){
					if ($group['title']) {
						$group['perm'] = serialize($group['perm']);
						usergroup_update_data(array('gid'=>$gid), $group);
					}
				}
			}
			
			$this->showSuccess('update_succeed');
		}else{
			$usergrouplist = usergroup_get_list(0,'creditslower ASC,gid ASC');
			if ($usergrouplist){
				$newgrouplist = array();
				foreach ($usergrouplist as $group){
					$group['perm'] = unserialize($group['perm']);
					$newgrouplist[$group['type']][] = $group;
				}
				$usergrouplist = $newgrouplist;
				unset($newgrouplist, $group);
			}else {
				$usergrouplist = array();
			}
			include template('member_group');
		}
	}
}