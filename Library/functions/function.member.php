<?php
/**
 * 显示登录界面
 */
function member_show_login(){
	global $G,$lang;
	$G['m'] = 'member';
	$G['title'] = $lang['login'];
	include template('login','member');
	exit();
}

/**
 * 显示AJAX登录界面
 */
function member_show_ajax_login(){
	global $G,$lang;
	$G['m'] = 'member';
	$G['title'] = $lang['login'];
	include template('ajaxlogin','member');
	exit();
}

/**
 * 显示微信版登录界面
 */
function member_show_weixin_login(){
	global $G,$lang;
	$G['m'] = 'weixin';
	$G['title'] = $lang['login'];
	include template('login','weixin');
	exit();
}

/**
 * 显示注册页面
 */
function member_show_register(){
	global $G,$lang;
	$G['m'] = 'weixin';
	$G['title'] = $lang['register'];
	include template('register','member');
	exit();
}

/**
 * 显示AJAX注册页面
 */
function member_show_ajax_register(){
	global $G,$lang;
	$G['m'] = 'member';
	$G['title'] = $lang['register'];
	include template('ajaxregister','member');
	exit();
}

/**
 * 显示微信版注册页面
 */
function member_show_weixin_register(){
	global $G,$lang;
	$G['m'] = 'weixin';
	$G['title'] = $lang['register'];
	include template('register','weixin');
	exit();
}

/**
 * 
 * @param string $password
 * @return string|string
 */
function member_encrypt_password($password){
	if (!$password) {
		return $password;
	}else {
		return sha1(md5($password));
	}
}

/**
 * 用户登录
 * @param string $username
 * @param string $password
 * @param string $field
 */
function member_login($username, $password, $field='username'){
	if (!$username || !$password) {
		return 3;
	}
	
	$field = in_array($field, array('uid','username','email','mobile')) ? $field : 'username';
	$member = M('member')->where(array($field=>$username))->getOne();
	if (!$member){
		return 1;
	}else  {
		if ($member['password'] !== member_encrypt_password($password)){
			return 2;
		}else {
			member_update_status($member['uid'], array(
					'lastvisit'=>TIMESTAMP,
					'lastvisitip'=>getIp()
			));
			unset($member['password']);
			cookie('dsxcms_member', authcode(serialize($member)));
			cookie('dsxcms_group', serialize(usergroup_get_data(array('gid'=>$member['uid']))));
			cookie('dsxcms_profile', serialize(member_get_profile($member['uid'])));
			cookie('dsxcms_status', serialize(member_get_status($member['uid'])));
			cookie('dsxcms_count', serialize(member_get_count($member['uid'])));
			return $member;
		}
	}
}

/**
 * 用户注册
 * @param array $data
 * @param number $login
 */
function member_register($data, $login=0){	
	$newmember = array();
	if (!$data['username'] && !$data['email'] && !$data['mobile']) {
		return 6;
	}else {
		if ($data['username']) {
			$newmember['username'] = $data['username'];
			if (member_get_num(array('username'=>$data['username']))) {
				//用户名已被人使用
				return 1;
			}
		}
			
		if ($data['mobile']) {
			$newmember['mobile'] = $data['mobile'];
			if (member_get_num(array('mobile'=>$data['mobile']))) {
				//手机号已被使用
				return 2;
			}
		}
			
		if ($data['email']) {
			$newmember['email'] = $data['email'];
			if (member_get_num(array('email'=>$data['email']))) {
				//邮箱已被人使用
				return 3;
			}
		}
	}
	
	if (!$data['password']) {
		return 5;
	}else {
		$newmember['password'] = member_encrypt_password($data['password']);
	}
	
	if ($data['gid']) {
		$newmember['gid'] = $data['gid'];
	}else {
		$group = M('member_group')->where("type='member' AND creditslower>=0")
		->order('creditslower','ASC')->getOne();
		$newmember['gid'] = $group['gid'];
	}
	
	$uid = member_add_data($newmember);
	M('member_profile')->insert(array('uid'=>$uid), false, true);
	M('member_status')->insert(array(
			'uid'=>$uid,
			'regdate'=>time(),
			'regip'=>getIp(),
			'lastvisit'=>time(),
			'lastvisitip'=>getIp()
	));
	
	M('member_count')->insert(array('uid'=>$uid), false, true);
	if (!$newmember['username']) {
		$newmember['username'] = 'dsx'.$uid;
		member_update_data(array('uid'=>$uid), array('username'=>$newmember['username']));
	}
	if ($login) {
		return member_login($uid, $data['password'], 'uid');
	}else {
		$member = member_get_data(array('uid'=>$uid));
		unset($member['password']);
		return $member;
	}
}

/**
 * 添加会员信息
 * @param array $data
 * @param boolean $return
 * @return unknown
 */
function member_add_data($data,$return=FALSE){
	$uid = M('member')->insert($data, true);
	if ($return) {
		return member_get_data(array('uid'=>$uid));
	}else {
		return $uid;
	}
}

/**
 * 删除会员信息
 * @param mixed $condition
 */
function member_delete_data($condition){
	if (!$condition){
		return false;
	}else {
		return M('member')->where($condition)->delete();
	}
}

/**
 * 删除详细资料
 * @param mixed $condition
 */
function member_delete_profile($condition){
	if (!$condition) {
		return false;
	}else {
		return M('member_profile')->where($condition)->delete();
	}
}

/**
 * 删除数据统计
 * @param mixed $condition
 */
function member_delete_count($condition){
	if (!$condition) {
		return false;
	}else {
		return M('member_count')->where($condition)->delete();
	}
}

/**
 * 删除状态
 * @param mixed $condition
 */
function member_delete_status($condition){
	if (!$condition) {
		return false;
	}else {
		return M('member_status')->where($condition)->delete();
	}
}

/**
 * 删除积分统计
 * @param mixed $condition
 */
function member_delete_score($condition){
	if (!$condition) {
		return false;
	}else {
		return M('member_score')->where($condition)->delete();
	}
}

/**
 * 更新会员信息
 * @param mixed $condition
 * @param array $data
 */
function member_update_data($condition,$data){
	return M('member')->where($condition)->update($data);
}

/**
 * 更新用户统计数据
 * @param int $uid
 * @param array $data
 */
function member_update_count($uid,$data){
	if (isset($data['uid'])){
		$data['uid'] = $uid;
	}
	return M('member_count')->where(array('uid'=>$uid))->update($data);
}

/**
 * 更新用户个人资料
 * @param int $uid
 * @param array $data
 */
function member_update_profile($uid,$data){
	if (isset($data['uid'])){
		$data['uid'] = $uid;
	}
	return M('member_profile')->where(array('uid'=>$uid))->update($data);
}

/**
 * 更新用户状态信息
 * @param int $uid
 * @param array $data
 */
function member_update_status($uid,$data){
	if (isset($data['uid'])){
		$data['uid'] = $uid;
	}
	return M('member_status')->where(array('uid'=>$uid))->update($data);
}

/**
 * 更新用户积分
 * @param int $uid
 * @param array $data
 */
function member_update_score($uid,$data){
	if (isset($data['uid'])){
		$data['uid'] = $uid;
	}
	return M('member_score')->where(array('uid'=>$uid))->update($data);
}

/**
 * 获取会员信息
 * @param mixed $condition
 */
function member_get_data($condition){
	$member = M('member')->where($condition)->getOne();
	if ($member) {
		$member['avatar'] = array(
				'big'=>avatar($member['uid'],'big'),
				'middle'=>avatar($member['uid'], 'middle'),
				'small'=>avatar($member['uid'],'small')
		);
		return $member;
	}else {
		return array();
	}
}

/**
 * 获取会员数量
 * @param mixed $condition
 */
function member_get_num($condition){
	return M('member')->where($condition)->count();
}

/**
 * 获取单用户统计数据
 * @param int $uid
 */
function member_get_count($uid){
	$count = M('member_count')->where(array('uid'=>$uid))->getOne();
	if (!$count) {
		M('member_count')->insert(array('uid'=>$uid));
		return member_get_count($uid);
	}else {
		return $count;
	}
}

/**
 * 获取用户基本资料
 * @param int $uid
 */
function member_get_profile($uid){
	$profile = M('member_profile')->where(array('uid'=>$uid))->getOne();
	if (!$profile){
		M('member_profile')->insert(array('uid'=>$uid));
		return member_get_profile($uid);
	}else {
		return $profile;
	}
}

/**
 * 获取用户状态信息
 * @param int $uid
 */
function member_get_status($uid){
	$status = M('member')->where(array('uid'=>$uid))->getOne();
	if (!$status) {
		M('member_status')->insert(array('uid'=>$uid));
		return member_get_status($uid);
	}else {
		return $status;
	}
}

/**
 * 获取用户积分
 * @param int $uid
 */
function member_get_score($uid){
	$score = M('member_score')->where(array('uid'=>$uid))->selectOne();
	if (!$score){
		M('member_score')->insert(array('uid'=>$uid,'score'=>0));
		return member_get_score($uid);
	}else {
		return $score;
	}
}

/**
 * 获取用户列表
 * @param mixed $condition
 * @param number $num
 * @param number $limit
 * @param string $order
 */
function member_get_list($condition,$num=20,$limit=0,$order=''){
	$limit = $num ? "$limit,$num" : '';
	$order = $order ? $order : 'uid ASC';
	$memberlist = M('member')->where($condition)->limit($limit)->order($order)->select();
	if ($memberlist){
		$datalist = array();
		foreach ($memberlist as $list){
			unset($list['password']);
			$list['avatar'] = array(
					'big'=>avatar($list['uid'], 'big'),
					'middle'=>avatar($list['uid'], 'middle'),
					'small'=>avatar($list['uid'], 'small')
			);
			$datalist[$list['uid']] = $list;
		}
		return $datalist;
	}else {
		return array();
	}
}

/**
 * 获取用户分页列表
 * @param mixed $condition
 * @param number $page
 * @param number $pagesize
 * @param string $order
 * @return NULL[][]
 */
function member_get_page($condition,$page=1, $pagesize=20, $order=''){
	$limit = ($page - 1)*$pagesize;
	return member_get_list($condition, $pagesize, $limit, $order);
}

/**
 * 获取附近会员列表
 * @param mixed $condition
 * @param float $lng
 * @param float $lat
 * @param number $num
 * @param number $limit
 */
function member_get_local_list($condition,$lng,$lat,$num=20,$limit=0){
	
}

/**
 * 获取附近会员分页列表
 * @param mixed $condition
 * @param float $lng
 * @param float $lat
 * @param number $page
 * @param number $pagesize
 */
function member_get_local_page($condition,$lng,$lat,$page=1,$pagesize=20){
	
}

/**
 * 获取用户权限
 * @param unknown $uid
 */
function member_get_perm($uid){
	$permission = M('member_perm')->where(array('uid'=>$uid))->getOne();
	if (!$permission) {
		M('member_perm')->insert(array('uid'=>$uid,'perm'=>serialize(array())), false, true);
		return member_get_perm($uid);
	}else {
		$permission['perm'] = unserialize($permission['perm']);
		return $permission;
	}
}

/**
 * 更新用户权限
 * @param mixed $uid
 * @param array $perm
 */
function member_update_perm($uid,$perm=array()){
	$perm = serialize($perm);
	return M('member_perm')->insert(array('uid'=>$uid,'perm'=>$perm), false, true);
}

/**
 * 添加会员分组
 * @param array $data
 * @param string $return
 */
function usergroup_add_data($data, $return=FALSE){
	$gid = M('member_group')->insert($data, true);
	if ($return){
		return usergroup_get_data(array('gid'=>$gid));
	}else {
		return $gid;
	}
}

/**
 * 删除用户组
 * @param mixed $condition
 */
function usergroup_delete_data($condition){
	if (!$condition) {
		return false;
	}else {
		return M('member_group')->where($condition)->delete();
	}
}

/**
 * 更新用户组信息
 * @param mixed $condition
 * @param array $data
 */
function usergroup_update_data($condition,$data){
	return M('member_group')->where($condition)->update($data);
}

/**
 * 获取用户组信息
 * @param mixed $condition
 */
function usergroup_get_data($condition){
	return M('member_group')->where($condition)->getOne();
}

/**
 * 获取用户分组列表
 * @param mixed $condition
 * @param string $order
 */
function usergroup_get_list($condition,$order=''){
	!$order && $order = 'gid ASC';
	$grouplist = M('member_group')->where($condition)->order($order)->select();
	if ($grouplist) {
		$datalist = array();
		foreach ($grouplist as $list){
			$datalist[$list['gid']] = $list;
		}
		return $datalist;
	}else {
		return array();
	}
}