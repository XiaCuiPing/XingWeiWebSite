// JavaScript Document
function Buy(id){
	if(DSXCMS.checkLogin()){
		window.location = '/?m=goods&c=buy&id='+id;
	}else {
		DSXCMS.showAjaxLogin();
	}
}

function addToCart(id){
	if(DSXCMS.checkLogin()){
		$.ajax({
			url:'/?m=goods&c=cart&a=add&id='+id,
			dataType:"json",
			success: function(json){
				if(json && json.errno==0){
					DSXUI.success('加入购物车成功');
				}else {
					alert('添加失败,请检查网络。');
				}
			}	
		
		});
	}else {
		DSXCMS.showAjaxLogin();
	}
}