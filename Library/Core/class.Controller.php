<?php
namespace Core;
abstract class Controller{
	protected $uid = 0;
	protected $username = '';
	protected $member = array();
	
	function __construct(){
		$this->uid      = $GLOBALS['G']['uid'];
		$this->username = $GLOBALS['G']['username'];
		$this->member   = $GLOBALS['G']['member'];
		ob_start();
	}
	
	public function t($tableName) {
		return M($tableName);
	}
	
	public function m($tableName) {
		return M($tableName);
	}
	
	/**
	 * 验证图形验证码
	 * @param string $code
	 */
	public function checkCaptchacode($code){
		$code = strtolower($code);
		if (!$code || ($code != cookie('captchacode'))){
			$this->showError('captchacode_verify_failed');
		}else {
			cookie('captchacode', null);
			return TRUE;
		}
	}
	
	/**
	 * 判断是否表单提交
	 */
	public function checkFormSubmit(){
		if ($_GET['formsubmit'] !== 'yes'){
			return false;
		}
		
		if ($_GET['formhash'] !== FORMHASH){
			return false;
		}
		return true;
	}
	
	/**
	 * 显示系统信息
	 * @param string $msg 提示信息
	 * @param string $type 信息类型
	 * @param string $forward 跳转页面
	 * @param array $links 可选链接
	 * @param string $tips 提示信息
	 * @param bool $autoredirect 是否自动跳转
	 */
	public function showMessage($msg='',$type='success',$forward='',$links=array(),$tips='',$autoredirect=false){
		global $G,$lang;
		$type = in_array($type, array('error', 'warning', 'information')) ? $type : 'success';
		$forward = $forward ? $forward : ($links ? $links[0]['url'] : $_SERVER['HTTP_REFERER']);
		if ($links){
			$newlinks = array();
			foreach ($links as $link){
				$link['text'] = $lang[$link['text']];
				$link['target'] = in_array($link['target'], array('_blank','_top','_self')) ? $link['target'] : '';
				$newlinks[] = $link;
			}
			$links = $newlinks;
			unset($newlinks);
		}
		$msg  = $msg ? $lang[$msg] : '';
		$tips = $lang[$tips];
		$G['title'] = $lang['system_message'];
		include template('message');
		exit();
	}
	public function showSuccess($msg,$forward='',$links=array(),$tips='',$autoredirect=false){
		$this->showmessage($msg,'success',$forward,$links,$tips,$autoredirect);
	}
	public function showError($msg,$forward='',$links=array(),$tips='',$autoredirect=false){
		$this->showmessage($msg,'error',$forward,$links,$tips,$autoredirect);
	}
	public function showWarning($msg,$forward='',$links=array(),$tips='',$autoredirect=false){
		$this->showmessage($msg,'warning',$forward,$links,$tips,$autoredirect);
	}
	public function showInformation($msg,$forward='',$links=array(),$tips='',$autoredirect=false){
		$this->showmessage($msg,'information',$forward,$links,$tips,$autoredirect);
	}
	public function notFound($message=''){
		!$message && $message = 'page_not_found';
		$this->showmessage($message,'error');
	}
	
	/**
	 * 无权限提示
	 * @param string $message
	 */
	public function noPermission($message=''){
		!$message && $message = 'no_permission';
		$this->showmessage($message,'error');
	}
	
	/**
	 * 未登录提示
	 */
	public function nologin(){
		$this->showmessage('nologin','information',array(
				array('text'=>$GLOBALS['lang']['click_login'],'url'=>'/?mod=member&ac=login'),
				array('text'=>$GLOBALS['lang']['go_back'],'url'=>'javascript:history.go(-1);'),
				array('text'=>$GLOBALS['lang']['go_home'],'url'=>'/')),'','',true);
	}
	
	/**
	 * 判断是否AJAX提交
	 */
	public function inAjax(){
		$inajax = isset($_GET['inajax']) ? intval($_GET['inajax']) : 0;
		return $inajax;
	}
	
	/**
	 * 返回Ajax数据
	 * @param id $data
	 */
	public function showAjaxReturn($data){
		echo json_encode(array('errno'=>0,'state'=>'success','data'=>$data));
		exit();
	}
	
	/**
	 * 返回Ajax错误信息
	 * @param unknown $errno
	 * @param string $error
	 * @param array $data
	 */
	public function showAjaxError($errno,$error='',$data=array()){
		echo json_encode(array('errno'=>$errno,'error'=>$error,'data'=>$data));
		exit();
	}
	
	/**
	 * 页面跳转
	 * @param string $url
	 */
	public function redirect($url){
		@header('location:'.$url);
		exit();
	}
	
	/**
	 * Discuz 风格分页
	 * @param int $curr_page 当前页
	 * @param int $pagecount 总页数
	 * @param int $totalnum 总记录
	 * @param string $extra 附加参数
	 * @param boolean $shownum 是否显示结果数目
	 */
	public function showPages($curr_page, $pagecount, $totalnum, $extra='', $shownum=FALSE){
		global $G,$lang;
		$multipage = '';
		$extra = $extra ? '&'.$extra : '';
		$url = '/?m='.$G['m'].'&c='.$G['c'].'&a='.$G['a'].$extra;
		if($pagecount>1){
			$page = 10;
			$offset = 2;
			$pages = $pagecount;
			$from = $curr_page-$offset;
			$to = $curr_page + $page - $offset - 1;
			if($page>$pages){
				$from=1;
				$to=$pages;
			}else{
				if($from<1){
					$to=$curr_page+1-$from;
					$from=1;
					if(($to-$from)<$page&&($to-$from)<$pages){
						$to=$page;
					}
				}elseif($to>$pages){
					$from=$curr_page-$pages+$to;
					$to=$pages;
					if(($to-$from)<$page&&($to-$from)<$pages){
						$from=$pages-$page+1;
					}
				}
			}
			
			$multipage = $shownum ? '<span>总计'.$totalnum.'条</span>' : '';
			if ($curr_page == 1){
				$multipage.= '';
			}else {
				$multipage .= "<a href=\"{$url}&page=1\">首页</a>";
				$multipage .= "<a href=\"{$url}&page=".($curr_page-1)."\">上一页</a>";
			}
			
			for($i=$from;$i<=$to;$i++){
				if($i!=$curr_page){
					$multipage.="<a href=\"{$url}&page=$i\">$i</a>";
				}else{
					$multipage.="<span class=\"cur\">$i</span>";
				}
			}
			
			if ($curr_page < ($pagecount-5)){
				
			}
			
			if ($curr_page < $pagecount){
				//$multipage.= $pages > $page ? "<span>...</span>" : '';
				$multipage.= "<a href=\"{$url}&page=".($curr_page+1)."\">下一页</a>";
				$multipage.= "<a href=\"{$url}&page=$pages\">尾页</a>";
			}
		}
		return   $multipage ;
	}
	
	/**
	 * google风格分页
	 * @param int $page 当前页
	 * @param int $total 总页数
	 * @param string $extra 附加参数
	 */
	public function googlePage($page,$total,$extra=''){
		$extra = !empty($extra) ? $extra.'&' : '';
		$scr = '/?m='.$G['m'].'&c='.$G['c'].'&a='.$G['a'].$extra;
		$prevs = $page-5;
		if($prevs<=0)$prevs = 1;
		$prev  = $page-1;
		if($prev<=0) $prev = 1;
		$nexts = $page+5;
		if($nexts>$total)$nexts=$total;
		$next  = $page+1;
		if($next>$total)$next=$total;
		$pagenavi ="<a href=\"{$scr}&page=1\">首页</a>";
		$pagenavi.="<a href=\"{$scr}&page=$prev\" class=\"prev\">上一页</a>";
		for($i=$prevs;$i<=$page-1;$i++){
			$pagenavi.="<a href=\"{$scr}&page=$i\">$i</a>";
		}
		$pagenavi.="<span class=\"cur\">$page</span>";
		for($i=$page+1;$i<=$nexts;$i++){
			$pagenavi.="<a href=\"{$scr}&page=$i\">$i</a>";
		}
		$pagenavi.="<a href=\"{$scr}&page=$next\" class=\"next\">下一页</a>";
		$pagenavi.="<a href=\"{$scr}&page=$total\">尾页</a>";
		return $pagenavi ;
	}
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	public function __call($name,$args){
		//die('Class "'.get_class($this).'" does not have a method named "'.$name.'".');
		throw new  \Exception('Class "'.get_class($this).'" does not have a method named "'.$name.'".');
	}
	
	function __destruct(){
		$content = ob_get_contents();
		ob_end_clean();
		echo $content;
	}
}