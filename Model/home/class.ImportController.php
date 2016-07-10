<?php
namespace Home;
class ImportController extends BaseController{
	private $action = '';
	public function index(){
		if ($this->action == 'category') {
			$json = httpGet('http://new.zhangwoo.cn/?m=home&c=api&a=getcategory');
			$datalist = json_decode($json, true);
			category_delete_data(array('type'=>'goods'));
			foreach ($datalist as $list){
				unset($list['catid']);
				category_add_data($list);
			}
			category_update_cache('goods');
		}
		
		if ($this->action == 'goods') {
			$json = httpGet('http://new.zhangwoo.cn/?m=home&c=api&a=getgoods');
			$datalist = json_decode($json, true);
			foreach ($datalist as $data){
				$goods = array(
						'catid'=>$data['catid'],
						'uid'=>$data['uid'],
						'shopid'=>$data['shopid'],
						'no'=>goods_create_no(),
						'name'=>$data['name'],
						'price'=>$data['price'],
						'stock'=>100,
						'isdiscount'=>$data['isdiscount'],
						'discount_price'=>$data['discount_price'],
						'dateline'=>$data['dateline']
				);
				$gid = goods_add_data($goods);
				image_add_data(array(
						'datatype'=>'goods',
						'dataid'=>$gid,
						'image'=>$data['imageurl'],
						'thumb'=>$data['thumburl'],
						'isremote'=>1
				));
			}
			echo 'complete';
		}
		
		if ($this->action == 'shop'){
			$json = httpGet('http://new.zhangwoo.cn/?m=home&c=api&a=getshop');
			$datalist = json_decode($json, true);
			foreach ($datalist as $data){
				$shop = array(
						'shopid'=>$data['shopid'],
						'catid'=>$data['catid'],
						'uid'=>$data['uid'],
						'shopname'=>$data['shopname'],
						'tel'=>$data['tel'],
						'province'=>$data['province'],
						'city'=>$data['city'],
						'county'=>$data['county'],
						'address'=>$data['address'],
						'dateline'=>$data['dateline'],
						'longitude'=>$data['longitude'],
						'latitude'=>$data['latitude']
				);
				$shopid = shop_add_data($shop);
				image_add_data(array(
						'datatype'=>'shop',
						'dataid'=>$shopid,
						'image'=>$data['imageurl'],
						'thumb'=>$data['thumburl'],
						'isremote'=>1
				));
			}
			echo 'complete';
		}
	}
}