<?php
namespace Member;
class HeadimgController extends BaseController{
	public function index(){
		global $G,$lang;
		$avatarsmall  = avatar($this->uid,'small');
		$avatarmiddle = avatar($this->uid,'middle');
		$avatarbig    = avatar($this->uid,'big');
		include template('headimg');
	}
	
	public function snap(){
		$filename = md5(time().rand(1000,9999)).'.jpg';
		$filepath = date('Y').'/'.date('m').'/'.$filename;
		$image = 'photo/'.$filepath;
		$thumb = 'thumb/'.$filename;
		@mkdir(dirname(C('ATTACHDIR').$image),0777,true);
		
		$content = file_put_contents(C('ATTACHDIR').$image, file_get_contents('php://input'));
		if (!$content) {
			$this->showAjaxError(-1, "ERROR: Failed to write data to $filepath, check permissions");
		}else {
			$img = new \Core\Image(C('ATTACHDIR').$image);
			$photo = array(
					'uid'=>$this->uid,
					'name'=>$filename.'.jpg',
					'image'=>$image,
					'thumb'=>$thumb,
					'width'=>$img->width(),
					'height'=>$img->height(),
					'type'=>$img->type(),
					'size'=>filesize(C('ATTACHDIR').$image),
					'dateline'=>time()
			);
			$img->thumb(210, 210);
			$img->save(C('ATTACHDIR').$thumb);
			photo_add_data($photo);
			$this->showAjaxReturn(array(
					'image'=>$image,
					'imageurl'=>image($image)
				)
			);
		}
	}
	
	public function upload(){
		if ($filedata = photo_upload_data()) {
			$this->showAjaxReturn(array(
					'image'=>$filedata['image'],
					'imageurl'=>$filedata['imageurl']
			));
		}else {
			$this->showAjaxError(-1, 'upload Error');
		}
	}
	
	public function crop(){
	
		$img = $_GET['img'];
		$src = C('ATTACHDIR').$_GET['src'];
		$avatardir    = C('AVATARDIR').$this->uid;
		$avatarsmall  = $this->uid.'_avatar_small.jpg';
		$avatarmiddle = $this->uid.'_avatar_middle.jpg';
		$avatarbig    = $this->uid.'_avatar_big.jpg';
		@mkdir($avatardir,0777,true);
		$image = new \Core\Image($src);
		$scale = $image->width()/300;
		$img['w'] = $img['w']*$scale;
		$img['h'] = $img['h']*$scale;
		$img['x'] = $img['x']*$scale;
		$img['y'] = $img['y']*$scale;
		$image->crop($img['w'], $img['h'],$img['x'],$img['y'],150,150);
		$image->save($avatardir.'/'.$avatarbig);
		$image->thumb(50, 50);
		$image->save($avatardir.'/'.$avatarmiddle);
		$image->thumb(30, 30);
		$image->save($avatardir.'/'.$avatarsmall);
		member_update_data(array('uid'=>$this->uid), array('avatarstatus'=>1));
		$this->showAjaxReturn('success');
	}
}