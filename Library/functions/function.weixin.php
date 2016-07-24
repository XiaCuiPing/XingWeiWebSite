<?php
/**
 * 微信登录
 * @param string $wxopenid
 */
function weixin_login($wxopenid){
	$member = member_get_data(array('wxopenid'=>$wxopenid));
	if ($member) {
		member_update_status($member['uid'], array(
				'lastvisit'=>TIMESTAMP,
				'lastvisitip'=>getIp()
		));
		member_update_cookie($member['uid']);
		return $member;
	}else {
		return false;
	}
}

/**
 * 微信注册
 * @param array $userinfo
 */
function weixin_register($userinfo){
	if ($userinfo['nickname'] && $userinfo['openid']){
		$group = M('member_group')->where("type='member' AND creditslower>=0")
		->order('creditslower','ASC')->getOne();
		$uid = member_add_data(array(
				'gid'=>$group['gid'],
				'username'=>$userinfo['nickname'],
				'wxopenid'=>$userinfo['openid']
		));
		M('member_profile')->insert(array('uid'=>$uid), false, true);
		M('member_status')->insert(array(
				'uid'=>$uid,
				'regdate'=>time(),
				'regip'=>getIp(),
				'lastvisit'=>time(),
				'lastvisitip'=>getIp()
		));

		M('member_count')->insert(array('uid'=>$uid), false, true);
		weixin_sync_headimg($uid, $userinfo['headimgurl']);
		return weixin_login($userinfo['openid']);
	}else {
		return false;
	}
}

/**
 * 获取code
 * @param string $appid
 * @param string $redirect_uri
 */
function weixin_get_code($appid,$redirect_uri){
	$weixin_uri = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid;
	$weixin_uri.= '&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
	header('location:'.$weixin_uri);
	exit();
}

/**
 * 获取微信用户信息
 * @param string $appid
 * @param string $appsecret
 * @param string $code
 * @param string $state
 * @return mixed
 */
function weixin_get_userinfo($appid,$appsecret,$code,$state=''){
	$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid;
	$url.= '&secret='.$appsecret.'&code='.$code.'&grant_type=authorization_code';
	$json   = httpGet($url);
	$data   = json_decode($json, true);
	$openid = $data['openid'];
	$access_token = $data['access_token'];

	$json = httpGet('https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN');
	return json_decode($json, true);
}

/**
 * 同步微信头像
 * @param int $uid
 * @param string $headimgurl
 */
function weixin_sync_headimg($uid,$headimgurl){
	$content = httpGet($headimgurl);
	$handle  = fopen(CACHE_PATH.$uid.'_avatar.jpg', 'w');
	if ($handle){
		fwrite($handle, $content);
		$avatardir    = C('AVATARDIR').$uid;
		$avatarsmall  = $uid.'_avatar_small.jpg';
		$avatarmiddle = $uid.'_avatar_middle.jpg';
		$avatarbig    = $uid.'_avatar_big.jpg';
		@mkdir($avatardir,0777,true);
		$image = new \Core\Image(CACHE_PATH.$uid.'_avatar.jpg');
		$image->thumb(150, 150);
		$image->save($avatardir.'/'.$avatarbig);

		$image->thumb(50, 50);
		$image->save($avatardir.'/'.$avatarmiddle);

		$image->thumb(30, 30);
		$image->save($avatardir.'/'.$avatarsmall);

		@unlink(CACHE_PATH.$uid.'_avatar.jpg');
	}
	fclose($handle);
}

function weixin_get_pay_sign(){

}