// JavaScript Document
function loadHotSale(){
	$.ajax({
		url:'/?m=home&c=index&a=showhot',
		success: function(c){
			$("#hot-sale-div").html(c);
		}	
	});
}
loadHotSale();