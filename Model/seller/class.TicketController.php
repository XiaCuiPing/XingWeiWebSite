<?php
namespace Seller;
class TicketController extends BaseController{
	public function index(){
		global $G,$lang;
		
		$G['title'] = '我的返券';
		include template('ticket');
	}
}