// JavaScript Document
function setImage(o,input){
	DSXUI.showImgUploadWindow({},function(c){
		if(c.errno == 0){
			$(o).attr('src', c.data.imageurl);
			$(input).val(c.data.image);
		}
	});
}

function switchContent(type){
	$(".swithContent").hide();
	$("#content-"+type).show();
}

var swfHandler = {
	onSelect : function(file){
		var k = file.id;
		var html = '<div class="item" id="'+k+'">'+
		'<div class="pic"></div>'+
		'<input type="hidden" class="image" name="piclist['+k+'][image]" value="">'+
	    '<input type="hidden" class="thumb" name="piclist['+k+'][thumb]" value="">'+
		'<textarea class="textarea" name="piclist['+k+'][description]" placeholder="在这里填写图片的说明"></textarea></div>';
		$("#swfuploadqueue").append(html);
	},
	onUploadSuccess:function(file, data, response){
		var json = $.parseJSON(data);
		if(json.errno == 0) {
			$("#"+file.id).find(".pic").html('<img src="'+json.data.imageurl+'">');
			$("#"+file.id).find(".image").val(json.data.image);
			$("#"+file.id).find(".thumb").val(json.data.thumb);
		}
	}
}

function initSwfUpload(formdata){
  var files = [];
  var imageCount = 0;
  var upload_options = {
      swf:'/static/uploadify/uploadify.swf',
      uploader:'/?m=common&c=upload&a=uploadimage&from=swfupload',
      queueID:'none',
      buttonClass:'submit post-swfupload-btn',
      auto:true,
      width:120,
      height:38,
      multi:true,
      queueSizeLimit:50,
      fileSizeLimit:'10MB',
      fileObjName:'filedata',
      buttonText:'<i class="icon">&#xf0175;</i>添加照片',
      fileTypeDesc:'图片',
      fileTypeExts:'*.jpg;*.jpeg;*.png;*.gif',
      removeCompleted:false,
      checkExisting:false,
      formData:formdata,
      onSelect:function(file){
		   imageCount++;
		   swfHandler.onSelect(file);
      },
	  onUploadStart : function(file){
		  $("#publishButton").attr('disabled', true);
	  },
      onQueueComplete:function(file,data,response){
		  $("#publishButton").attr('disabled', false);
      },
      onUploadSuccess:function(file, data, response){
		  swfHandler.onUploadSuccess(file, data, response);
      }
  }
  $("#swfuploadButton").uploadify(upload_options);
}

;$(function(){
	$("#postForm").submit(function(e) {
        var title = $.trim($("#postTitle").val());
		if(title.length < 1){
			DSXUI.error("标题不能为空");
			return false;
		}
		return true;
    });
})