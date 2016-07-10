<?php
namespace Core;
class UploadImage extends Upload{
	public function saveImage(){
		$setting = G('setting');
		$max_width  = isset($setting['img_max_width']) ? $setting['img_max_width'] : 1200;
		$max_height = isset($setting['img_max_height']) ? $setting['img_max_height'] : 1200;
		$thumb_width  = isset($setting['img_thumb_width']) ? $setting['img_thumb_width'] : 210;
		$thumb_height = isset($setting['img_thumb_height']) ? $setting['img_thumb_height'] : 210;
		
		$this->maxsize = isset($setting['img_maxsize']) ? $setting['img_maxsize']*1024 : 10485760;
		$this->allowtypes = array('jpg','jpeg','png','gif');
		$filename = $this->setfilename();
		$filepath = date('Y').'/'.date('m').'/'.$filename;
		
		$image = 'photo/'.$filepath;
		$thumb = 'thumb/'.$filepath;
		$fullpath = C('ATTACHDIR').$image;
		if ($this->save($fullpath)){
			$filesize = $this->size();
			$img = new Image($fullpath);
			if ($setting['img_limit_size']){
				$img->thumb($max_width, $max_height);
				$img->save($fullpath);
				$filesize = filesize($fullpath);
			}
			
			if ($setting['img_water_mark']){
				if ($setting['img_warter_type'] == 0){
					$img->water(ROOT_PATH.$setting['img_water_img']);
				}else {
					$img->text($setting['img_water_text'], $setting['img_water_font'], $setting['img_water_fontsize']);
				}
			}
			
			$img->thumb($thumb_width, $thumb_height);
			$img->save(C('ATTACHDIR').$thumb);
			return array(
					'name'=>$filename,
					'width'=>intval($img->width()),
					'height'=>intval($img->height()),
					'type'=>$img->type(),
					'size'=>$filesize,
					'image'=>$image,
					'thumb'=>$thumb
			);
		}else {
			return false;
		}
	}
}