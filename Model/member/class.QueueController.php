<?php
namespace Member;
class QueueController extends BaseController{
	public function index(){
		global $G,$lang;
		
		include template('queue');
	}
}