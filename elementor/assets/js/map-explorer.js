// These are defaults
var mapbox_api_key = 'pk.eyJ1IjoieGl4aWNpdHkiLCJhIjoiY2tqY2R5NGs0MHRxdDJ4cXZ1eWhzb2RycyJ9.Td_gMYC6Bs4Q3IUar9M1DA';
var mapbox_id = 'xixicity/ckjcejkm4k7mx1at4hx43xqn5';

var map_explorer = '';


( function( $ ) {
 	"use strict";
    
    /**
     * Map Explorer
     *
     * loads and OSM map
     */
    var MapExplorer = elementorModules.frontend.handlers.Base.extend({  //elementorModules.ViewModule.extend({

  		onInit: function onInit() {

      		var elementSettings = this.getElementSettings();

			if( $('#map_explorer').length ) {

				if( elementSettings['mapbox_api_key'] ) {
					mapbox_api_key = elementSettings['mapbox_api_key'];
				}

				if( elementSettings['mapbox_id'] ) {
					mapbox_id = elementSettings['mapbox_id'];
				}

				var map_attribution = elementSettings['map_attribution'];

			
			    // Create control for About panel
			    var AboutControl =  L.Control.extend({        
			        options: {
			            position: 'topleft'
			        },

			        onAdd: function (map) {
			            var container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-about');
			            var button = L.DomUtil.create('a', 'leaflet-control-about-button');
			            button.append('All');

			            container.append(button);
			            // container.style.backgroundColor = 'white';     
			            // container.style.backgroundImage = "url(http://t1.gstatic.com/images?q=tbn:ANd9GcR6FCUMW5bPn8C4PbKak2BJQQsmC-K9-mbYBeFZm1ZM2w2GRy40Ew)";
			            // container.style.backgroundSize = "30px 30px";
			            // container.style.width = '30px';
			            // container.style.height = '30px';

			            container.onclick = function(){
			            	map_explorer.fitBounds(LatLngs, { padding: [10,10] });
					    	history.pushState(null, null, window.location.pathname + window.location.search);
			            }

			            return container;
			        }
			    });

			    // add custom markers
				L.MakiMarkers.accessToken = mapbox_api_key;
				var icon = L.MakiMarkers.icon({icon: "circle", color: "#95c9cc", size: "m"});

				map_explorer = L.map('map_explorer', {
		  			zoomSnap: 0, // http://leafletjs.com/reference.html#map-zoomsnap
		  			icon: icon
				}).setView([22.289805,114.1910298], 16);

				L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
				    attribution: map_attribution,
				    maxZoom: 18,
				    id: mapbox_id,
				    tileSize: 512,
				    zoomOffset: -1,
                  	detectRetina: true,
				    accessToken: mapbox_api_key
				}).addTo(map_explorer);

				if( map_locations.length ) {

					// map_locations.each(function(marker) {
					// 	add_marker( 22.2866648, 114.1389192, 'My Old Address');
					// });

					var LatLngs = [];
					var geoJsonLayer = L.geoJSON( map_locations, {
						style: function (feature) {
		        			return { color: '#95c9cc' };
		    			},
		    			onEachFeature: function (feature, layer) {
						    var lat = feature.geometry.coordinates[1];
						    var lng = feature.geometry.coordinates[0];

						  	LatLngs.push(feature.geometry.coordinates);

						  	layer.setIcon(icon);

						    layer.bindPopup('<h4>'+feature.properties.name+'</h4>');
							layer.on('mouseover', function (e) {
					            this.openPopup();
					        });
					        layer.on('mouseout', function (e) {
					            this.closePopup();
					        });
					        layer.on('click', function (e) {
					            window.location = '#'+feature.properties.url;
					            //console.log([lat, lng]);
					    		map_explorer.setView( [lat, lng] );
					        });
						}
					});//.addTo(map_explorer);

					//var map_locations = [{"type":"Feature","geometry":{"type":"Point","coordinates":[ 114.1389192, 22.2866648 ]},"properties":{"name":"Xi Xi won the 2019 Newman Prize for Chinese Literature."}}];
					//L.geoJSON( map_locations ).addTo(map_explorer);

					map_explorer.fitBounds(LatLngs, { padding: [10,10] });

					var markers = new L.MarkerClusterGroup({singleMarkerMode:false});
		    		markers.addLayer(geoJsonLayer);
					map_explorer.addLayer(markers);
					
				}

				//add_marker( 22.2866648, 114.1389192, 'My Old Address');


				$('.on-map').click(function() {
					// hightlight article section
					$('article.timeline').removeClass('highlighted');
					var thisLocation = $(this).parents('article.timeline');
					thisLocation.addClass('highlighted');
					

					var id = thisLocation.attr('id').substring(5);
					var newhash = "#location-"+id;
					if(history.pushState) {
					    history.pushState(null, null, newhash);
					}
					else {
					    location.hash = newhash;
					}
					window.dispatchEvent(new HashChangeEvent('hashchange'));
				});

				window.addEventListener('hashchange', this.zoomToMarker);


			    // Add About button
			    map_explorer.addControl(new AboutControl());
				

				map_explorer.on('load', function () {
				    map_explorer.resize();
				});


				if(window.location.hash) {
					this.zoomToMarker();
				}

			}
		},

		// Zoom to marker based upon hash
		zoomToMarker: function zoomToMarker() {
			// zoom on marker
			var id = window.location.hash.substring(10);
			 //console.log(id);
			var feature = this.getGeojsonProperties(map_locations, 'id', id);
			 //console.log(feature);

		    var lat = feature.geometry.coordinates[1];
		    var lng = feature.geometry.coordinates[0];
			map_explorer.setView( [lat, lng], 15 );

			$('article.timeline').removeClass('highlighted');
			$('#location-'+id).parents('article.timeline').addClass('highlighted');
		},

		/**
		 * Search GeoJSON for property
		 */
		getGeojsonProperties: function getGeojsonProperties(geojsons, key, value) {
			for(var i = 0; i < geojsons.length; i++) {
				//console.log(geojsons[i].properties);
				if(geojsons[i].properties[key] == value) {
					return geojsons[i];
				}
			}
			return false;
		},

		/**
		 * Add a marker
		 */
		addMarker: function addMarker( lat, long, title ) {

			var options = {
				'title': title,
			};
			var marker = L.marker([lat, long], options).addTo(map_explorer);
			//marker.bindPopup(popupContent).openPopup();


			marker.on('click', function() {
				alert('This is '+title);
			});

			return true;

		}

	});

    // Load Elementor Handlers
    $( window ).on( 'elementor/frontend/init', function() {

        const addMapExplorer = ( $element ) => {
            elementorFrontend.elementsHandler.addHandler( MapExplorer, {
                $element,
            } );
        };
        elementorFrontend.hooks.addAction( 'frontend/element_ready/map-explorer.default', addMapExplorer );


	});

} ( jQuery ) );