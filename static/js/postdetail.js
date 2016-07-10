// JavaScript Document
function updateComments(id,type){type = type||0;$.get('/?m=post&c=addon&a=updatecomments&id='+id+'&type='+type);}
function deletePost(id){
	if(!id) return;
	var dlg = DSXUI.dialogConfirm({
		title:'删除确认',
		text:'你确定要删除此文章吗?',
		callback:function(){
			$.ajax({
				url:'/?mod=post&act=addon&opt=deletepost&id='+id,
				dataType:"json",
				success: function(json){
					dlg.close();
					if(json.state){
						DSXUI.success('文章删除成功',function(){
							window.location = '/';
						});
					}
				}	
			});
		}
	})
}
function setPostState(id,state){
	$.ajax({
		url:'/?mod=post&act=addon&opt=setstate&id='+id+'&state='+state,
		dataType:"json",
		success: function(json){
			if(json.state){
				DSXUI.success('审核成功');
			}
		}
	});
}
function Favorite(id){
	if(!id) return false;
	$.ajax({
		url:'/?mod=post&act=addon&opt=favorite&id='+id,
		dataType:"json",
		success: function(json){
			if(json.state){
				DSXUI.success('收藏成功');
			}else{
				DSXUI.error(json.info);
			}
		}
	});
}
function joinActive(id){
	if(!id) return false;
	$.ajax({
		url:'/?mod=post&act=addon&opt=joinactive&activeid='+id,
		dataType:"json",
		success: function(json){
			if(json.state){
				DSXUI.success('谢谢你的参与,请确保你的资料中联系方式有效');
			}else{
				DSXUI.error(json.info);
			}
		}
	});
}
function dsxcms_load_comment(){
	$.ajax({
		url:'/?mod=post&act=comment&opt=loadcomment&post_id='+GLOBAL_CONFIG.postdata.id,
		beforeSend: function(){$("#comment-loading").show();},
		success:function(html){
			$("#comment-loading").hide();
			$("#post-comment-list-content").html(html);
		}
	});
}
function deleteComment(o,commid){
	DSXUI.confirm(o,'你确定要删除此评论吗?',function(){
		$.ajax({
			url:'/?mod=post&act=comment&opt=deletecomment&commid='+commid,
			dataType:"json",
			success: function(json){
				if(json.state){
					GLOBAL_CONFIG.postdata.comments--;
					DSXUI.success('评论删除成功');
					updateComments(GLOBAL_CONFIG.postdata.id,1);
					$("#comment_item_"+commid).slideUp('slow',function(){$(this).remove();});
				}
			}
		});
	});	
}

;(function(){
	dsxcms_load_comment();
	$("#comment-text").focus(function(){$("#comment-tips").hide();});
	if(GLOBAL_CONFIG.islogin){
	$("#comment-publish").click(function(){
		if(!$.trim($("#comment-text").val())){
			$("#comment-tips").show(); return false;
		}else{
			$("form[name=formcomment]").ajaxSubmit({
				url:'/?mod=post&act=comment&opt=savecomment&post_id='+GLOBAL_CONFIG.postdata.id,
				dataType:'json',
				success:function(json){//alert(json);
					if(json.state){
						DSXUI.success('评论发表成功');
						updateComments(GLOBAL_CONFIG.postdata.id);
						$("form[name=formcomment]")[0].reset();
						dsxcms_load_comment();
					}
				}
			});
		}
	});
	}
})();
(function($){
$.fn.playPhoto = function(settings){
	var settings = $.extend({},settings);
	var _this = this,offset = $(this).offset(),_index=0;
	var li = $(this).find('ul>li');
	var pic = $(this).find('ul>li>img');
	li.eq(_index).show().siblings().hide();
	pic.mouseenter(function(){
		$(_this).find('.cur-next').css({'right':'0','top':$(this).height()/2-90});
		$(_this).find('.cur-prev').css({'left':'0','top':$(this).height()/2-90});
	}).mousemove(function(e){
		e = e||window.event;
		if(e.clientX<(offset.left+$(_this).width()/2)){
			$(_this).find('.cur-next').hide();
			$(_this).find('.cur-prev').show();
		}else{
			$(_this).find('.cur-next').show();
			$(_this).find('.cur-prev').hide();
		}
	});
	$(this).mouseleave(function(){
		$(_this).find('.cur-next').hide();
		$(_this).find('.cur-prev').hide();
	});
	$(_this).find('.cur-prev').click(function(){
		_index--;
		if(_index<0){
			alert('已经到第一张了');
			_index = 0;
		}
		li.eq(_index).show().siblings().hide();
	});
	$(_this).find('.cur-next').click(function(){
		_index++;
		if(_index>=li.length){
			alert('已经到最后一张了');
			_index = li.length-1;
		}
		li.eq(_index).show().siblings().hide();
	});
}
})(jQuery);
$("#dsxcms-slider").playPhoto();