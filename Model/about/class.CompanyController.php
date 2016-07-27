<?php
namespace About;
class CompanyController extends BaseController{
	public function detail(){
		global $G,$lang;
		
		$id = intval($_GET['id']);
		$company = company_get_data(array('id'=>$id));
		include template('company_detail');
	}
}