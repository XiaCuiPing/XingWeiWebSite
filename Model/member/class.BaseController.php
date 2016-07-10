<?php
namespace Member;
use Core\Controller;
class BaseController extends Controller{
	function __construct(){
		parent::__construct();
		if (!$this->uid || !$this->username) {
			member_show_login();
		}
	}
}