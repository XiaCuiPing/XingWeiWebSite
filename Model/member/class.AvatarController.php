<?php
namespace Member;
use Core\Controller;
class AvatarController extends Controller{
	public function index(){
		$uid  = intval($_GET['uid']);
		$size = trim($_GET['size']);
		$size = in_array($size, array('middel','small')) ? $size : 'big';
		$avatar = $uid.'/'.$uid.'_avatar_'.$size.'.jpg';
		if (is_file(C('AVATARDIR').$avatar)){
			$avatar = C('AVATARURL').$avatar;
		}else {
			$avatar = getsiteurl().'/static/images/common/avatar_default.png';
		}
		@header('location:'.$avatar);
		exit();
	}
}