var mymap = '';

jQuery(document).ready( function($) {


	mymap = L.map('mapid').setView([22.289805,114.1910298], 12);

	L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
	    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
	    maxZoom: 18,
	    id: 'mapbox/streets-v11',
	    tileSize: 512,
	    zoomOffset: -1,
	    accessToken: 'pk.eyJ1IjoiMTAwMGNhbWVscyIsImEiOiJja2oyMXdlOXYwcXJ4MnFsajdudGpnajQ3In0.OHk4Qv5z76H7TtMSa81Okg'
	}).addTo(mymap);



	//add_marker( 22.2866648, 114.1389192, 'My Old Address');



} ( jQuery ) );



/**
 * Add a marker
 */
 function add_marker( lat, long, title ) {

	var options = {
		'title': title,
	};
	var marker = L.marker([lat, long], options).addTo(mymap);
	//marker.bindPopup(popupContent).openPopup();


	marker.on('click', function() {
		alert('This is '+title);
	});

	return true;

}