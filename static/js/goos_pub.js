// JavaScript Document
$("#selectImageButton").mouseover(function(){
	$("#filedata").css({'left':$(this).offset().left,'top':$(this).offset().top})
	.width($(this).width()).height($(this).height());
});

var imagenum = 0;
var imagekey = 0;
$("#filedata").change(function(){
	var picItem;
	$("#uploadForm").ajaxSubmit({
		dataType:'json',
		beforeSend:function(){
			picItem = $('<div class="imageUploadItem"></div>').insertBefore("#selectImageButton");
		},
		success:function(json){
			if(json.errno == 0){
				var del = $('<a class="icon del" href="javascript:;" title="删除">&#xf00b3;</a>').on('click',function(){
					$(this).parent().remove();
					imagenum--;
				});
				picItem.append('<img src="'+json.data.imageurl+'" />');
				picItem.append('<input type="hidden" name="piclist['+imagekey+'][image]" value="'+json.data.image+'">');
				picItem.append('<input type="hidden" name="piclist['+imagekey+'][thumb]" value="'+json.data.thumb+'">');
				picItem.append(del);
				imagenum++;
				imagekey++;
			}else {
				alert('上传失败('+json.error+')');
			}
		}
	});
});
$("#goodsForm").submit(function(){
	if(($("#goodsname").val()).length < 2){
		$("#tips_name").show();
		return false;
	}else {
		$("#tips_name").hide();
	}
	
	if(!$("#cat3").val()){
		$("#tips_catid").show();
		return false;
	}else {
		$("#tips_catid").hide();
	}
	
	if(!$("#price").val()){
		$("#tips_price").show();
		return false;
	}else {
		$("#tips_price").hide();
	}
	return true;
});

$("#cat1").change(function(e) {
    var fid = $(this).val();
	var theoptions = '';
	if(fid == '0') return;
	for(var k in categoryoptions[fid]){
		var data = categoryoptions[fid][k];
		theoptions+= '<option value="'+data.catid+'">'+data.cname+'</option>';
	}
	$("#cat2").html(theoptions);
});
$("#cat2").change(function(e) {
    var fid = $(this).val();
	var theoptions = '';
	if(fid == '0') return;
	for(var k in categoryoptions[fid]){
		var data = categoryoptions[fid][k];
		theoptions+= '<option value="'+data.catid+'">'+data.cname+'</option>';
	}
	$("#cat3").html(theoptions);
});