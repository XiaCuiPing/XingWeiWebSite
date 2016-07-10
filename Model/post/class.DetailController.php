<?php
namespace Post;
class DetailController extends BaseController{
	public $id = 0;
	public $catid = 0;
	public function index(){
		global $G,$lang;
		$this->id = intval($_GET['id']);
		post_update_viewnum($this->id);
		$article = post_get_data(array('id'=>$this->id));
		$article['tags'] = $article['tags'] ? unserialize($article['tags']) : array();
		if (!in_array($article['type'], array('image','video','music','goods','active'))){
			$article['type'] = 'article';
		}
		
		$this->catid = $article['catid'];
		$category = category_get_data(array('catid'=>$catid));
		$G['title'] = $article['title'].' - '.$category['cname'];
		
		$G['keywords'] = $article['tags'] ? implode(',', $article['tags']) : $G['keywords'];
		$G['description'] = $article['summary'] ? $article['summary'] : $G['keywords'];
		
		if ($article['type'] == 'article'){
			$content['content'] = post_get_content($this->id);
			include template('detail_article');
		}
		if ($article['type'] == 'image'){
			$piclist = image_get_list($id, 'article');
			include template('detail_image');
		}
		
		if($article['type'] == 'video'){
			$content['content'] = post_get_content($this->id);
			$video = unserialize($content['content']);
			include template('detail_video');
		}
	}
}