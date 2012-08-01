if (typeof ics != 'object')
	ics = {};

ics.geoloc = function() {
};

ics.geoloc.prototype.initMap = function(myOptions) {
	position =  new google.maps.LatLng(myOptions.lat, myOptions.lng);
	zoom = myOptions.zoom;
	mapTypeId = google.maps.MapTypeId.ROADMAP;
	var options = {
		'zoom': zoom,
		'center': position,
		'mapTypeId': mapTypeId,
		'mapTypeControl': false,
		'streetViewControl': false,
	};
	map = new google.maps.Map(document.getElementById("map_canvas"), options);
	marker = new google.maps.Marker({
		'map': map,
		'position': position
	});
};
