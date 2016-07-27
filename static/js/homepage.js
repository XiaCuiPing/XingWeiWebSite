// JavaScript Document
function loadHotSale(){
	$.ajax({
		url:'/?m=market&c=index&a=showhot',
		success: function(c){
			$("#hot-sale-div").html(c);
		}	
	});
}
loadHotSale();