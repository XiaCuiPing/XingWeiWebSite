<?php
namespace Admin;
class MemberController extends BaseController{
	public function index(){
		$this->showlist();
	}
	
	/**
	 * 显示会员列表
	 */
	public function showlist(){
		if ($this->checkFormSubmit()){
			$uids = $_GET['uid'];
			if ($uids && is_array($uids)){
				$uids = implode(',', $uids);
				$condition = array('uid'=>array('IN', $uids));
				switch ($_GET['option']){
					case 'delete':
						member_delete_data($condition);
						member_delete_profile($condition);
						member_delete_status($condition);
						member_delete_count($condition);
						break;
					
					case 'nologin':
						member_update_data($condition, array('status'=>-1));
						break;
					
					case 'nopost':
						member_update_data($condition, array('status'=>-2));
						break;
					default:;
				}
				$this->showSuccess('update_succeed');
			}else {
				$this->showError('no_select');
			}
		}else {
			global $G,$lang;
			$pagesize = 50;
			$condition = array();
			
			$field   = isset($_GET['field']) ? trim($_GET['field']) : '';
			$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
			if ($field && $keyword){
				switch ($field) {
					case 'uid': $condition['uid'] = $keyword;
					break;
					
					case 'username': $condition['username'] = array('LIKE', $keyword);
					break;
					
					case 'mobile' : $condition['mobile'] = array('LIKE', $keyword);
					break;
					
					case 'email' : $condition['email'] = array('LIKE', $keyword);
					break;
					 default: $condition['username'] = array('LIKE', $keyword);
				}
			}
			
			$totalnum   = member_get_num($condition);
			$pagecount  = $totalnum < $pagesize ? 1 : ceil($totalnum/$pagesize);
			$memberlist = member_get_page($condition, $G['page'], $pagesize, 'uid ASC');
			$usergrouplist = usergroup_get_list(0);
			
			$uids = array_keys($memberlist);
			$uids = !empty($uids) ? implode(',', $uids) : 0;
			$memberstatuslist = $this->t('member_status')->where("uid IN($uids)")->select();
			$memberstatus = array();
			if ($memberstatuslist){
				foreach ($memberstatuslist as $status){
					$memberstatus[$status['uid']] = $status;
				}
			}
			unset($memberstatuslist, $status);
			$pages = $this->showPages($G['page'], $pagecount, $totalnum, "field=$field&keyword=$keyword", 1);
			include template('member_list');
		}
	}
	
	public function edit(){
		$uid = intval($_GET['uid']);
		if ($this->checkFormSubmit()) {
			$membernew = $_GET['membernew'];
			if ($membernew['password']) {
				$membernew['password'] = member_encrypt_password($membernew['password']);
			}else {
				unset($membernew['password']);
			}
			member_update_data(array('uid'=>$uid), $membernew);
			
			$permission = $_GET['permission'];
			if ($permission && is_array($permission)) {
				$permission = is_array($permission) ? serialize($permission) : $permission;
				M('member_perm')->where(array('uid'=>$uid))->update(array('perm'=>$permission));
			}
			$this->showSuccess('update_succeed');
		}else {
			global $G,$lang;
			$member = member_get_data(array('uid'=>$uid));
			$permission = member_get_perm($uid);
			$permission = $permission ? $permission['perm'] : array();
			$grouplist  = usergroup_get_list(0);
			include template('member_form');
		}
	}
	
	public function create(){
		global $G,$lang;
		if ($this->checkFormSubmit()) {
			$errno = 0;
			$membernew = $_GET['membernew'];
			cookie('membernew',serialize($membernew),600);
			if ($membernew['username'] && $membernew['password']) {
				$returns = member_register($membernew);
				if (is_array($returns) && $returns['uid']){
					$permission = $_GET['permission'];
					if ($permission && is_array($permission)) {
						$permission = is_array($permission) ? serialize($permission) : $permission;
						M('member_perm')->insert(array('uid'=>$returns['uid'], 'perm'=>$permission), false, true);
					}
					cookie('membernew', null);
					$this->showSuccess('save_succeed');
				}else {
					L('errmsg', $lang['member_register_error'][$returns]);
					$this->showError('errmsg');
				}
			}else {
				$this->showError('undefined_action');
			}
		}else {
			
			$grouplist = usergroup_get_list(0);
			$member = unserialize(cookie('membernew'));
			include template('member_form');
		}
	}
}