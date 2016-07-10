<?php
namespace Common;
class EditorapiController extends BaseController{
	public function uploadimage(){
		if ($_GET['uploadtype'] == 'swfupload'){
			$uid = intval($_GET['uid']);
			$username = trim($_GET['username']);
			$token = sha1($uid.$username.formhash());
			if ($uid && $username && $token == $_GET['token']){
				$this->uid = $uid;
				$this->username = $username;
				G('uid',$this->uid);
				G('username', $this->username);
			}
		}
		
		if (!$this->uid || !$this->username){
			$this->showAjaxError(-1,'nologin');
		}
		
		if ($filedata = photo_upload_data('imgFile')){
			echo json_encode(array('error'=>0, 'url'=>$filedata['imageurl']));
			exit();
		}else {
			$this->showAjaxError(1, 'upload fail');
		}
	}
	
	public function selectimage(){
		header('Content-type: application/json; charset=UTF-8');
		$photolist = $this->t('photo')->limit(0,100)->select();
		$filelist = array();
		if ($photolist){
			foreach ($photolist as $list) {
				$filelist[] = image($list['attachment']);
			}
		}
		$result = array();
		//相对于根目录的上一级目录
		$result['moveup_dir_path'] = '';
		//相对于根目录的当前目录
		$result['current_dir_path'] = '';
		//当前目录的URL
		$result['current_url'] = '';
		//文件数
		$result['total_count'] = count($filelist);
		//文件列表数组
		$result['file_list'] = $filelist;
		echo json_encode($result);
		exit();
	}
}