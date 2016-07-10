// JavaScript Document
var Util = {
	event:function(event){
		var e = event || window.event;
		return e;
	},
	hasClass:function(el,name){
		return $(el).attr('class').split(/\s+/).indexOf(name)!=-1;
	},
	getTarget:function(e){
		var target = e.target||e.srcElement;
		if(target != null){
			return target;
		}
	},
	getScroll:function(){
		var t = $(document).scrollTop();
		var l = $(document).scrollLeft();
		var w = $(document).width();
		var h = $(document).height();
		return {t:t,l:l,w:w,h:h};
	},
	getRandom : function (len,type) {
		len = len < 0 ? 0 : len;
		type = type && type<=3? type : 3;
		var str = '';
		for (var i = 0; i < len; i++) {
			var j = Math.ceil(Math.random()*type);
			if (j == 1) {
				str += Math.ceil(Math.random()*9);
			} else if (j == 2) {
				str += String.fromCharCode(Math.ceil(Math.random()*25+65));
			} else {
				str += String.fromCharCode(Math.ceil(Math.random()*25+97));
			}
		}
		return str;
	},
	getMousePos:function(e){
		return {
			x : e.pageX || e.clientX + document.body.scrollLeft,
			y : e.pageY || e.clientY + document.body.scrollTop
		}
	}
}
var DIY = function(){
	this.data = [];
	this.frames = [];
	this.blocks = [];
	this.frameSelector  = ".frame";
	this.columnSelector = ".column";
	this.blockSelector  = ".block";
	this.areaSelector   = ".diy";
	this.frameTitleClass = "frame-title";
	this.blockTitleClass = "block-title";
	this.isChange = false;
	this.placeholder = '';
	this.dragObj = '';
	this.overObj = '';
}

DIY.prototype = {
	getPlaceHolder:function(){
		if(!this.placeholder){
			this.placeholder = $('<div class="ui-sortable-placeholder" id="ui-placeholder"></div>');
			this.placeholder.css({height:30});
		}
		return this.placeholder;
	},
	getFrameHtml:function(type){
		var id = 'frame'+Util.getRandom(6);
		var str = '<div id="'+id+'" class="frame">';
		if(type == 1){
			str+= '<div id="'+id+'_title" class="frame-title"><span class="icon delete">&#xf0155;</span><span class="titletext">1框架</span></div>';
			str+= '<div id="'+id+'_main" class="column frame-c"></div>';
		}else if(type == 2){
			str+= '<div id="'+id+'_title" class="frame-title"><span class="icon delete">&#xf0155;</span><span class="titletext">1-1框架</span></div>';
			str+= '<div id="'+id+'_left" class="column frame-1-1-l"></div>';
			str+= '<div id="'+id+'_right" class="column frame-c"></div>';
		}else if(type == 3){
			str+= '<div id="'+id+'_title" class="frame-title"><span class="icon delete">&#xf0155;</span><span class="titletext">1-1-1框架</span></div>';
			str+= '<div id="'+id+'_left" class="column frame-1-1-1-l"></div>';
			str+= '<div id="'+id+'_center" class="column frame-1-1-1-c"></div>';
			str+= '<div id="'+id+'_right" class="column frame-c"></div>';
		}else if(type == 4){
			str+= '<div id="'+id+'_title" class="frame-title"><span class="icon delete">&#xf0155;</span><span class="titletext">1-2框架</span></div>';
			str+= '<div id="'+id+'_left" class="column frame-1-2-l"></div>';
			str+= '<div id="'+id+'_right" class="column frame-c"></div>';
		}else if(type == 5){
			str+= '<div id="'+id+'_title" class="frame-title"><span class="icon delete">&#xf0155;</span><span class="titletext">2-1框架</span></div>';
			str+= '<div id="'+id+'_left" class="column frame-2-1-l"></div>';
			str+= '<div id="'+id+'_right" class="column frame-c"></div>';
		}else if(type == 6){
			str+= '<div id="'+id+'_title" class="frame-title"><span class="icon delete">&#xf0155;</span><span class="titletext">1-2-2框架</span></div>';
			str+= '<div id="'+id+'_left" class="column frame-1-2-2-l"></div>';
			str+= '<div id="'+id+'_center" class="column frame-1-2-2-c"></div>';
			str+= '<div id="'+id+'_right" class="column frame-c"></div>';
		}else if(type == 7){
			str+= '<div id="'+id+'_title" class="frame-title"><span class="icon delete">&#xf0155;</span><span class="titletext">2-2-1框架</span></div>';
			str+= '<div id="'+id+'_left" class="column frame-2-2-1-l"></div>';
			str+= '<div id="'+id+'_center" class="column frame-2-2-1-c"></div>';
			str+= '<div id="'+id+'_right" class="column frame-c"></div>';
		}
		return str+'</div>';
	},
	getBlockHtml:function(){
		var id = 'block'+Util.getRandom(6);
		var str = '<div id="'+id+'" class="block"></div>';
		return str;
	},
	crateFrame:function(e,type){
		var e = Util.event(e);
		this.overObj = $(this.getFrameHtml(type));
		this.dragObj = $('<div class="dragObj"></div>').appendTo("body");
		var scrolltop = $(document).scrollTop();
		var scrollleft = $(document).scrollLeft();
		this.dragObj.lastMouseX = e.clientX;
		this.dragObj.lastMouseY = e.clientY;
		this.dragObj.css({'position':'absolute','z-index':'3000','cursor':'move',left:e.clientX+scrollleft-40,top:e.clientY+scrolltop-3});
		$(document).disableSelection();
		var that = this;
		var frames = [];
		var colums = [];
		$(this.frameSelector).each(function(i, el) {
            var p = $(el).offset();
			el['frame'] = {x:p.left,y:p.top,w:$(el).width(),h:$(el).height()};
			frames[i] = el;
        });
		$(this.columnSelector).each(function(i, el) {
            var p = $(el).offset();
			el['frame'] = {x:p.left,y:p.top,w:$(el).width(),h:$(el).height()};
			colums[i] = el;
        });
		this.dragObj.drag = function(e){
			var e = Util.event(e);
			var _clientX = e.clientX;
			var _clientY = e.clientY;
			var position = that.dragObj.offset();
			if(_clientX == position.left && _clientY == position.top) return false;
			var left = position.left+_clientX-that.dragObj.lastMouseX;
			var top  = position.top+_clientY-that.dragObj.lastMouseY;
			that.dragObj.css({left:left,top:top});
			that.dragObj.lastMouseX = _clientX;
			that.dragObj.lastMouseY = _clientY;
			that.getPlaceHolder();
			var current = {x:_clientX+$(document).scrollLeft(),y:_clientY+$(document).scrollTop()};
			var target = Util.getTarget(e);
			if(frames.length>0){
				for(var i=0; i<frames.length; i++){
					var frame = frames[i]['frame'];
					if(current.x > frame['x'] && current.x < frame['x'] + frame['w'] && current.y > frame['y'] && current.y < frame['y'] + frame['h']/2){
						if(target != frames[i]){
							that.placeholder.insertBefore(frames[i]);
							return;
						}
					}else if(current.x > frame['x'] && current.x < frame['x'] + frame['w'] && current.y > frame['y'] + frame['h']/2 && current.y < frame['y'] + frame['h']){
						if(target != frames[i]){
							that.placeholder.insertAfter(frames[i]);
							return;
						}
					}else{
						$(that.areaSelector).each(function(index, element) {
							var offset = $(element).offset();
							  var size = {w:$(element).width(),h:$(element).height()};
							  if(current.x > offset.left && current.x < offset.left+size.w && current.y > offset.top && current.y < offset.top+size.h){
								  $(element).append(that.placeholder);
							  }
						});
					}
				}
			}
			else{
				//$($(that.areaSelector)[0]).prepend(that.placeholder);
				$(that.areaSelector).each(function(index, element) {
                    var offset = $(element).offset();
					  var size = {w:$(element).width(),h:$(element).height()};
					  if(current.x > offset.left && current.x < offset.left+size.w && current.y > offset.top && current.y < offset.top+size.h){
						  $(element).append(that.placeholder);
					  }
                });
			}
		};
		this.dragObj.enddrag = function(e){
			//DIY.prototype.endDrag.call(that,e);
			e = Util.event(e);
			$(document).enableSelection();
			$(document).unbind('mousemove',that.dragObj.drag);
			$(document).unbind('mouseup',that.dragObj.enddrag);
			if(that.placeholder[0].parentNode){
				if(that.placeholder[0].parentNode == document.body){
					that.placeholder.remove();
					that.dragObj.remove();
				}else{
					that.overObj.insertBefore(that.placeholder);
					that.placeholder.remove();
					that.dragObj.remove();
					that.bindFrmaeAction();
					that.isChange = true;
				}
			}else{
				that.dragObj.remove();
			}
		}
		$(document).bind('mousemove',this.dragObj.drag);
		$(document).bind('mouseup',this.dragObj.enddrag);
	},
	createBlock:function(e,type,name){
		var e = Util.event(e);
		this.overObj = $(this.getBlockHtml());
		this.overObj.contentType = type;
		this.overObj.name = name;
		this.dragObj = $('<div class="dragObj"></div>').appendTo("body");
		//this.dragObj = $('<div><img src="/static/images/diy/model_'+type+'.png" border="0"></div>').appendTo("body");
		var scroll = Util.getScroll();
		this.dragObj.lastMouseX = e.clientX;
		this.dragObj.lastMouseY = e.clientY;
		this.dragObj.css({'position':'absolute','z-index':'3000','cursor':'move',left:e.clientX+scroll.l-40,top:e.clientY+scroll.t-10});
		$(document).disableSelection();
		var blocks = [];
		var columns = [];
		var that = this;
		$(this.columnSelector).each(function(i, el) {
			var p = $(el).offset();
			el['frame'] = {x:p.left,y:p.top,w:$(el).width(),h:$(el).height()};
            columns[i] = el;
        });
		
		$(this.blockSelector).each(function(i, el) {
			var p = $(el).offset();
			el['frame'] = {x:p.left,y:p.top,w:$(el).width(),h:$(el).height()};
            blocks[i] = el;
        });
		if(columns.length == 0){
			alert("未找到框架，请先添加框架"); 
			this.dragObj.remove();
			return false;
		}
		this.dragObj.drag = function(e){
			var e = Util.event(e);
			var _clientX = e.clientX;
			var _clientY = e.clientY;
			var position = that.dragObj.offset();
			if(_clientX == position.left && _clientY == position.top) return false;
			var left = position.left + _clientX-that.dragObj.lastMouseX;
			var top  = position.top + _clientY-that.dragObj.lastMouseY;
			that.dragObj.css({left:left,top:top});
			that.dragObj.lastMouseX = _clientX;
			that.dragObj.lastMouseY = _clientY;
			that.getPlaceHolder();
			
			var current = {x:_clientX+$(document).scrollLeft(),y:_clientY+$(document).scrollTop()};
			var target = Util.getTarget(e);
			for(var i=0; i<blocks.length; i++){
				var frame = blocks[i]['frame'];
				if(current.x > frame['x'] && current.x < frame['x'] + frame['w'] && current.y > frame['y'] && current.y < frame['y'] + frame['h']/2){
					if(target != blocks[i]){
						that.placeholder.insertBefore(blocks[i]);
						return;
					}
				}else if(current.x > frame['x'] && current.x < frame['x'] + frame['w'] && current.y > frame['y'] + frame['h']/2 && current.y < frame['y'] + frame['h']){
					if(target != blocks[i]){
						that.placeholder.insertAfter(blocks[i]);
						return;
					}
				}
			}
			for(var i=0; i<columns.length; i++){
				var frame = columns[i]['frame'];
				if(current.x > frame['x'] && current.x < frame['x'] + frame['w'] && current.y > frame['y'] && current.y < frame['y'] + frame['h']){
					if(target != columns[i]){
						if($(columns[i]).find(that.blockSelector).length==0){
							that.placeholder.prependTo(columns[i]);
							break;
						}
					}
				}
			}

		}
		this.dragObj.enddrag = function(e){
			e = Util.event(e);
			that.dragObj.remove();
			$(document).enableSelection();
			$(document).unbind('mousemove',that.dragObj.drag);
			$(document).unbind('mouseup',that.dragObj.enddrag);
			if(that.placeholder[0].parentNode){
				if(that.placeholder[0].parentNode == document.body){				
					that.placeholder.remove();
				}else{
					$.ajax({
						url:'/?mod=diy&act=openblock&type='+that.overObj.contentType+'&name='+that.overObj.name,
						async:false,
						dataType:"html",
						success: function(html){
							var dlg = dialog(html,{
								title:'添加模块',
								callback:function(){
									if(!$("#formblock").find("input[name=name]").val()){
										DSXUI.error('模块名称不能为空');
									}else{
										$("#formblock").ajaxSubmit({
											url:'/?mod=diy&act=saveblock&type='+that.overObj.contentType+'&name='+that.overObj.name,
											dataType:'json',
											success:function(json){
												if(json.state){
													$.ajax({
														url:'/?mod=diy&act=getblockhtml&blockid='+json.blockid,
														dataType:"html",
														success: function(html2){
															that.overObj.attr('blockid',json.blockid);
															that.overObj.html(html2);
															that.placeholder.replaceWith(that.overObj)
															//that.overObj.insertBefore(that.placeholder);
															//that.placeholder.remove();
															that.bindBlockAction();
															that.isChange = true;
															dlg.close();
														}
													});
												}else if(!json.blockid){
													DSXUI.error('模块名称已被使用');
												}
											}
										});
									}
								},
								afterClose:function(){
									if(that.placeholder){
										that.placeholder.remove();
									}
								}
							});
						}
					});
					
				}
			}
		}
		$(document).bind('mousemove',this.dragObj.drag);
		$(document).bind('mouseup',this.dragObj.enddrag);
	},
	bindBlockAction:function(){
		var that = this,div;
		div = $('<div class="block-edit"><span node="edit">编辑</span><span node="delete">删除</span></div>').show();
		$(this.blockSelector).each(function(i, el) {
			$(el).mouseenter(function(){
				//div.mouseleave(function(){$(this).remove();}).show();
				$(this).append(div);
				$(this).find("[node=edit]").die('click').one('click',function(){that.openBlockEdit(el);});
				$(this).find("[node=delete]").die('click').one('click',function(){
					if(confirm('你确定要删除此模块吗?')){
						$(el).remove();
					}
				});
			}).mouseleave(function(){
				div.remove();
			});
	
        });
		$(this.columnSelector).sortable({connectWith:this.columnSelector,accept:this.blockSelector,change:function(){that.isChange = true;}});
	},
	bindFrmaeAction:function(){
		var that = this;
		$(this.frameSelector).each(function(i, el) {
            $(el).find(".delete").die('click').live('click',function(){
				if(confirm('你确定要删除此框架吗,删除后将不能恢复。')){
					$(this).parent().parent().slideUp('slow',function(){$(this).remove();});
				}
			});
        });
		$(this.areaSelector).sortable({handle:'.frame-title',accept:this.frameSelector,change:function(){that.isChange = true;}});
	},
	openBlockEdit:function(block){
		if(!block) return false;
		block = $(block);
		blockid = block.attr("blockid");
		if(!blockid) return false;
		$.ajax({
			url:'/?mod=diy&act=openblockedit&blockid='+blockid,
			dataType:'html',
			success: function(html){
				var dlg = dialog(html,{
					title:'编辑模块',
					callback:function(){
						$("#formblock").ajaxSubmit({
							url:'/?mod=diy&act=updateblock&blockid='+blockid,
							dataType:'json',
							success:function(json){
								if(json.state){
									$.ajax({
										url:'/?mod=diy&act=getblockhtml&blockid='+blockid,
										dataType:"html",
										success: function(html2){
											block.html(html2);
											dlg.close();
										}
									});
								}else{
									DSXUI.error('程序错误，请稍后再试');
								}
							}
						});
						//dlg.close();
					}
				});
			}	
		});
	},
	getPosition:function(){
		this.data = [];
		var areas = $(this.areaSelector);
		for(var n=0; n<areas.length; n++){
            var area = {};
			 area.name = areas[n].id;
			 area.frames = [];
			 var frames = $(areas[n]).find(this.frameSelector);
			for(var k=0; k<frames.length; k++) {
				var frame = {};
				frame.name = frames[k].id;
				frame.className = frames[k].className;
				frame.title = $(frames[k]).find(".titletext").text();
				frame.columns = [];
				var columns = $(frames[k]).find(this.columnSelector);
				for(var i=0; i<columns.length; i++){
					var column = {};
					column.name = columns[i].id;
					column.className = columns[i].className;
					column.blocks = [];
					var blocks = $(columns[i]).find(this.blockSelector);
					for(var j=0; j<blocks.length; j++){
						var block = {};
						block.name = blocks[j].id;
						block.className = blocks[j].className;
						if($(blocks[j]).attr('blockid')){
							block.idvalue = $(blocks[j]).attr('blockid');
						}else{
							continue;
						}
						column.blocks.push(block);
					}
					frame.columns.push(column);
				}
				area.frames.push(frame);
			}
			this.data.push(area);
        };
		//alert('ok');
	},
	getPostionStr:function(){
		this.getPosition();
		var start = '<?xml version="1.0" encoding="utf-8"?><root>';
		var end ="</root>";
		var str = '';
		for(var i in this.data){
			str+= this.getAreaXML(this.data[i]);
		}
		return start+str+end;
	},
	getAreaXML:function(area){
		var str = '<area>';
		str+= '<name>'+area.name+'</name>';
		for(var i in area.frames){
			str+= this.getFrameXML(area.frames[i]);
		}
		return str+'</area>';
	},
	getFrameXML:function(frame){
		var attrstr = this.getFrameAttrXML(frame);
		return '<frame>'+attrstr+'</frame>';
	},
	getFrameAttrXML:function(frame){
		var str = '<name>'+frame.name+'</name>';
		str+= '<className>'+frame.className+'</className>';
		str+= '<title>'+frame.title+'</title>';
		for(var i in frame.columns){
			str+= this.getColumnXML(frame.columns[i]);
		}
		return str;
	},
	getColumnXML:function(column){
		var str = '<name>'+column.name+'</name>';
		str+= '<className>'+column.className+'</className>';
		for(var i in column.blocks){
			str+= this.getBlockXML(column.blocks[i]);
		}
		return '<column>'+str+'</column>';
	},
	getBlockXML:function(block){
		var str = '<name>'+block.name+'</name>';
		str+= '<className>'+block.className+'</className>';
		str+= '<blockid>'+block.idvalue+'</blockid>';
		return '<block>'+str+'</block>';
	},
	save:function(filename){
		window.onbeforeunload = '';
		var xml = Diy.getPostionStr();
		if(!filename) {
			if(DIY_CONFIG.filename){
				filename = DIY_CONFIG.filename;
			}else{
				return false;
			}
		}
		$.ajax({
			url:'/?mod=diy&act=save&filename='+filename,
			type:'POST',
			data:{xml:xml},
			dataType:"json",
			success: function(json){
				if(json.state){
					DSXUI.success('页面保存成功',function(){
						window.location.href = window.location.href.replace(/[\?|\&+]diy\=yes/g,'');
					});
				}
			}
		});
	},
	cancel:function(){
		this.getPosition();
		if(this.isChange){
			window.onbeforeunload = function() {
				return '您的数据已经修改,退出将无法保存您的修改。';
			};
		}
		window.location.href = window.location.href.replace(/[\?|\&+]diy\=yes/g,'');
	},
	init:function(){
		this.bindBlockAction();
		this.bindFrmaeAction();
		this.getPosition();
		this.olddata = this.data;
	},
	extend : function (obj) {
		for (var i in obj) {
			this[i] = obj[i];
		}
	}
};
var Diy = new DIY();
Diy.init();