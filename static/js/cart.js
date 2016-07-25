// JavaScript Document
function removeFromCart(goods_id){
	this.remove = function(){
		$.ajax({
			url:'/?m=goods&c=cart&a=remove&goods_id='+goods_id,
			dataType:'json',
			success: function(json){
				if(json.errno == 0){
					$("#item-"+goods_id).remove();
				}else {
					alert('移除失败');
				}
			}
		});
	}
	
	if(confirm('你确定要删除此商品吗?')){
		this.remove();
	}
}

function changeNumber(goods_id, type){
	var num = parseInt($("#number_"+goods_id).val());
	if(type == 0){
		num--;
	}else {
		num++;
	}
	if(num < 1) num = 1;
	$("#number_"+goods_id).val(num);
	
	var price = parseFloat($("#price_"+goods_id).attr("value"));
	var total = price * num;
	$("#total_"+goods_id).attr("value", total).html('￥'+total.toFixed(2));
	Total();
}

function Total(){
	var total_amount = 0;
	$(".mark:checked").each(function(i, el) {
        var amount = $("#total_"+$(el).val()).attr("value");
		total_amount+= parseFloat(amount);
    });
	$("#total_amount").html('￥'+total_amount.toFixed(2));
}

$(".mark").click(function(e) {
    Total();
});

$(".shopmark").click(function(e) {
    $(".shop_"+$(this).val()).attr('checked', true);
	Total();
});

$("#checkall").click(function(e) {
    if($(this).is(":checked")){
		$(".mark,.shopmark").attr('checked', true);
	}else{
		$(".mark,.shopmark").attr('checked', false);
	}
	Total();
});

$(document).ready(function(e) {
    Total();
});