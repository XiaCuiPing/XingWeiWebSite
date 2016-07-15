<?php
namespace Member;
class ScoreController extends BaseController{
	public function index(){
		global $G,$lang;
		
		include template('score');
	}
}