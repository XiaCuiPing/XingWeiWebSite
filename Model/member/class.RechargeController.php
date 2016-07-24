<?php
namespace Member;
class RechargeController extends BaseController{
	public function index(){
		global $G,$lang;
		
		$G['title'] = '账户充值';
		include template('recharge');
	}
}