// JavaScript Document
;(function($){
$.fn.Jscroll = function(settings){
	settings = $.extend({
		speed : 3000,
		animateSpeed:1000,
		direction : 'left',
		width:300,
		height:300,
		arrowLeft:'',
		arrowRight:''
	},settings);
	var _this = this;
	var ul = $(this).find("ul");
	$(this).css({'overflow':'hidden','height':settings.height});
	if(settings.direction == 'left' || settings.direction == 'right') ul.css({'width':10000,'height':settings.height});
	_this.t = setInterval(function(){_this.AutoPlay();},settings.speed);
	_this.scrollLeft = function(){
		ul.animate({marginLeft:-settings.width+"px"},settings.animateSpeed,function(){
			//把第一个li丢最后面去
			ul.css({marginLeft:0}).find("li:first").appendTo(ul);
		});
	}
	_this.scrollRight = function(){
		ul.css({marginLeft:-settings.width+"px"}).find("li:last").prependTo(ul);
		ul.animate({marginLeft:0},settings.animateSpeed,function(){
			//把第一个li丢最后面去
		});
	}
	_this.scrollUp = function(){
		ul.animate({marginTop:-settings.height+"px"},settings.animateSpeed,function(){
			//把第一个li丢最后面去
			ul.css({marginTop:0}).find("li:first").appendTo(ul);
		});
	}
	_this.scrollDown = function(){
		ul.css({marginTop:-settings.height+"px"}).find("li:last").prependTo(ul);
		ul.animate({marginTop:0},settings.animateSpeed,function(){
			//把第一个li丢最后面去
		});
	}
	_this.AutoPlay = function(){
		if(settings.direction == 'right'){
			_this.scrollRight();
		}else if(settings.direction == 'up'){
			_this.scrollUp();
		}else if(settings.direction == 'down'){
			_this.scrollDown();
		}else{
			_this.scrollLeft();
		}
	}
	$(this).hover(function(){
		clearTimeout(_this.t);
	},
	function(){
		_this.t = setInterval(function(){_this.AutoPlay();},settings.speed);
	});
	$(this).find(settings.arrowLeft).bind('click',_this.scrollRight);
	$(this).find(settings.arrowRight).bind('click',_this.scrollLeft);
}
})(jQuery);