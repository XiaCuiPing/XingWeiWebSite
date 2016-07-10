// JavaScript Document
var map = new AMap.Map('mapcontainer',{
	zoom: 13
});

if(!window.point[0] || !window.point[1]) {
	window.point = [0,0];
	setCurrentLocation();
}else {
	map.setCenter(window.point);
}

function setCurrentLocation(){
	var geolocation;
	map.plugin('AMap.Geolocation', function() {
		geolocation = new AMap.Geolocation({
			enableHighAccuracy: false,//是否使用高精度定位，默认:true
			timeout: 20000,          //超过10秒后停止定位，默认：无穷大
			buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
			zoomToAccuracy: true,      //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
		});
		//map.addControl(geolocation);
		geolocation.getCurrentPosition();
		AMap.event.addListener(geolocation, 'complete', function(data){
			var lng = data.position.getLng();
			var lat = data.position.getLat();
			//alert('lng:'+lng+',lat:'+lat);
			window.point = [lng,lat];
			setMarker(window.point);
		});//返回定位信息
		AMap.event.addListener(geolocation, 'error', function(err){
		});      
		//返回定位出错信息
	});
}

var marker = new AMap.Marker({
	draggable:true,
	map:map
});
AMap.event.addListener(marker,'dragend',setLocation);
setMarker(window.point);

function changeCity(city){
	if(!city) return;
	/*
	AMap.plugin('AMap.Geocoder',function(){
        var geocoder = new AMap.Geocoder();
		var address = $("#province").val() + ' ' + $("#city").val()+' '+$("#county").val();
		geocoder.getLocation(address,function(status,result){
			if(status=='complete' && result.geocodes.length){
				setMarker(result.geocodes[0].location);
			  	map.setCenter(marker.getPosition())
			  	return;
			}else{
			  
			}
		});
    });
	*/
	
	AMap.plugin('AMap.DistrictSearch',function(){
		var district = new AMap.DistrictSearch({
			//高德行政区划查询插件实例
			subdistrict: 2   
			//返回下一级行政区
		});
		district.setLevel('city');
		district.search(city, function(status, result) {
			if(status=='complete' && result.districtList.length){
				//注意，api返回的格式不统一，在下面用三个条件分别处理
			
				var districtData = result.districtList[0];
				//alert(districtData.center.getLng());
				map.setCenter(districtData.center);
				setMarker(districtData.center);
			}else {
				//alert(status+':'+result);
			}
		});
	});
}

function setMarker(point){
	marker.setPosition(point);
	//$("#longitude").val(point[0]);
	//$("#latitude").val(point[1]);
	var location = marker.getPosition();
	$("#longitude").val(location.getLng());
	$("#latitude").val(location.getLat());
}

function setLocation(e){
	//alert(e.target.getPosition().getLng());
	var point = e.target.getPosition();
	$("#longitude").val(point.getLng());
	$("#latitude").val(point.getLat());
}

function fullScreen(){
	$("#mapcontainer").css({
		'position':'absolute',
		'z-index':'1000',
		'top':0,
		'left':0
	}).width($(document).width()).height($(document).height()).appendTo("body");
	var closediv = $("<div/>").css({
		'position':'fixed',
		'z-index':'2000',
		'top':0,
		'right':0,
		'display':'block',
		'background':'#fff',
		'font-size':'20px',
		'padding':'10px 15px'
	}).text('关闭地图').click(function(e) {
        $(this).remove();
		$("#mapcontainer").css({
			'position':'relative',
			'z-index':'0',
			'top':'auto',
			'left':'auto'
		}).width(500).height(300).appendTo("#map");
    });;
	$("#mapcontainer").append(closediv);
}