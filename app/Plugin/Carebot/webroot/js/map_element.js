/* 
* @Author: sebb
* @Date:   2014-06-12 22:15:27
* @Last Modified by:   sebb
* @Last Modified time: 2014-06-13 00:40:20
*/

$(document).ready(function(){

	var loc;

	var map = new google.maps.Map($('.map')[0], {
		center: new google.maps.LatLng(-34.397, 150.644),
		zoom: 12
	});

	$.get(document.location.href + '.json', {limit:200}, function(response) {
		var bounds = new google.maps.LatLngBounds();
		var prevLoc = null;
		$.each(response.data, function(index, item) {
			prevLoc = loc;
			loc = new google.maps.LatLng(item.Location.latitude, item.Location.longitude);

			bounds.extend(loc);

			var marker = new google.maps.Marker({
			    position: loc,
			    map: map
			});

			if(prevLoc != null) {
				var line = new google.maps.Polyline({
					path: [
						prevLoc,
						loc
					],
					geodesic: true,
					strokeColor: '#FF0000',
					strokeOpacity: 1.0,
					strokeWeight: 2,
					map:map
				});
			}
		});

		map.fitBounds(bounds);
		map.panToBounds(bounds);
	});

});