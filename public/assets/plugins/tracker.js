jQuery(document).ready(function($) {
    'use strict';
    var routeSelect = document.getElementById('routeSelect');
    var map = document.getElementById('map-canvas');
    var autoRefresh = false;
    var intervalID = 0;
    var sessionIDArray = [];
    var viewingAllRoutes = false;
        
var map = L.map('map-canvas'),
    realtime = L.realtime({
        url: baseurl +'tracker/get_tracker',
        crossOrigin: true,
        type: 'json'
    }, {
        interval: 3 * 1000,
	pointToLayer: function (feature, latlng) { 
		
		if (feature.properties.speed > 50) {
			var iconType =  imgurl +'coolred_small.png';
		} else {
			var iconType =  imgurl +'coolgreen2_small.png';
		}
		
		var azimuth = "";
       	 if ((feature.properties.direction >= 337 && feature.properties.direction <= 360) || (feature.properties.direction >= 0 && feature.properties.direction < 23))
               	 azimuth =  "compassN";
        	if (feature.properties.direction >= 23 && feature.properties.direction < 68)
        		azimuth =  "compassNE";
        	if (feature.properties.direction >= 68 && feature.properties.direction < 113)
                	 azimuth =  "compassE";
       	 if (feature.properties.direction >= 113 && feature.properties.direction < 158)
                	 azimuth =  "compassSE";
        	if (feature.properties.direction >= 158 && feature.properties.direction < 203)
               	 azimuth =  "compassS";
       	 if (feature.properties.direction >= 203 && feature.properties.direction < 248)
                	 azimuth =  "compassSW";
       	 if (feature.properties.direction >= 248 && feature.properties.direction < 293)
                 	azimuth =  "compassW";
        	if (feature.properties.direction >= 293 && feature.properties.direction < 337)
                 	azimuth =  "compassNW";
 

        // convert from meters to feet
       var accuracy = parseInt(feature.properties.accuracy * 3.28);

        var popupWindowText = "<table border=0 style=\"font-size:95%;font-family:arial,helvetica,sans-serif;color:#000;\">" +
            "<tr><td align=right>&nbsp;</td><td>&nbsp;</td><td rowspan=2 align=right>" +
          "<img src=http://localhost/gpstracker/assets/images/" + azimuth + ".jpg alt= />" + " </td></tr>" +
            "<tr><td align=right>Speed:&nbsp;</td><td>" + feature.properties.speed +  " mph</td></tr>" +
            "<tr><td align=right>Distance:&nbsp;</td><td>" + feature.properties.distance +  " mi</td><td>&nbsp;</td></tr>" +
            "<tr><td align=right>Time:&nbsp;</td><td colspan=2>" + feature.properties.gpsTime +  "</td></tr>" +
            "<tr><td align=right>Name:&nbsp;</td><td>" + feature.properties.userName + "</td><td>&nbsp;</td></tr>" +
            "<tr><td align=right>Accuracy:&nbsp;</td><td>" + accuracy + " ft</td><td>&nbsp;</td></tr></table>";
		 
		 
	        return L.marker(latlng, {
	            'icon': L.icon({
	                iconUrl: iconType,
	               // shadowUrl: '//leafletjs.com/docs/images/leaf-shadow.png',
	                iconSize:     [30, 30], // size of the icon
	               //  shadowSize:   [50, 64], // size of the shadow
	              //  iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
	               // shadowAnchor: [4, 62],  // the same for the shadow
	               // popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
	            })
	        }
		).bindPopup(popupWindowText);
	    } 
    }
).addTo(map); 

L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {	 
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);


/*
var basemap = L.tileLayer.wms("http://localhost:8080/geoserver/KPP/wms", 
{
    layers: 'KPP:Kalteng',
    format: 'image/png',
    transparent: true 
});
map.addLayer(basemap);*/


realtime.on('update', function() {
    map.fitBounds(realtime.getBounds(), {});	//maxZoom: 15
});

	//getalldeviceonlinetoday();
    //getAllRoutesForMap();
    //loadRoutesIntoDropdownBox();
    
    $("#routeSelect").change(function() {
        if (hasMap()) {
            viewingAllRoutes = false;
            
            getRouteForMap();
        } 
    });
           
        
    function getalldeviceonlinetoday() {
        viewingAllRoutes = true;
        routeSelect.selectedIndex = 0;
        showPermanentMessage('Please select a route below');
                   
        $.ajax({
            url: baseurl +'tracker/getalldeviceonlinetoday',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                loadGPSLocations(data);
        },
        error: function (xhr, status, errorThrown) {
            console.log("error status: " + xhr.status);
            console.log("errorThrown: " + errorThrown);
        }
        });
    }           
    function getAllRoutesForMap() {
        viewingAllRoutes = true;
       // routeSelect.selectedIndex = 0;
        showPermanentMessage('Please select a route below');
                   
        $.ajax({
            url: baseurl +'tracker/getallroutesformap',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                loadGPSLocations(data);
        },
        error: function (xhr, status, errorThrown) {
            console.log("error status: " + xhr.status);
            console.log("errorThrown: " + errorThrown);
        }
        });
    }           
        
    function loadRoutesIntoDropdownBox() {      
        $.ajax({ 
            url: baseurl +'tracker/getroutes',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
            loadRoutes(data);
        },
        error: function (xhr, status, errorThrown) {
            console.log("status: " + xhr.status);
            console.log("errorThrown: " + errorThrown);
        }
        });
    }    
    
    function loadRoutes(json) {        
        if (json.length == 0) {
            showPermanentMessage('There are no routes available to view');
        }
        else {
            // create the first option of the dropdown box
            var option = document.createElement('option');
            option.setAttribute('value', '0');
            option.innerHTML = 'Select Route...';
            routeSelect.appendChild(option);

            // when a user taps on a marker, the position of the sessionID in this array is the position of the route
            // in the dropdown box. it's used below to set the index of the dropdown box when the map is changed
            sessionIDArray = [];
            
            // iterate through the routes and load them into the dropdwon box.
            $(json.routes).each(function(key, value){
                var option = document.createElement('option');
                option.setAttribute('value',  $(this).attr('sessionID'));

                sessionIDArray.push($(this).attr('sessionID'));

                option.innerHTML = $(this).attr('userName') + " " + $(this).attr('times');
                routeSelect.appendChild(option);
            });

            // need to reset this for firefox
            routeSelect.selectedIndex = 0;

            showPermanentMessage('Please select a route below');
        }
    }

    function getRouteForMap() { 
        if (hasMap()) {
            // console.log($("#routeSelect").prop("selectedIndex"));  

            $.ajax({
                   url: baseurl +'tracker/getrouteformap/' + $('#routeSelect').val(),
                   type: 'GET',
                   dataType: 'json',
                   success: function(data) {
                      loadGPSLocations(data);
                   },
                   error: function (xhr, status, errorThrown) {
                       console.log("status: " + xhr.status);
                       console.log("errorThrown: " + errorThrown);
                    }
               });
        
        } 
    }

    function loadGPSLocations(json) {
        // console.log(JSON.stringify(json));
        
        if (json.length == 0) {
            showPermanentMessage('There is no tracking data to view');
            map.innerHTML = '';
        }
        else {
            if (map.id == 'map-canvas') {
                // clear any old map objects
                document.getElementById('map-canvas').outerHTML = "<div id='map-canvas'></div>";
           
                // use leaflet (http://leafletjs.com/) to create our map and map layers
                var gpsTrackerMap = new L.map('map-canvas');
            
                var openStreetMapsURL = ('https:' == document.location.protocol ? 'https://' : 'http://') +
                 '{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
                var openStreetMapsLayer = new L.TileLayer(openStreetMapsURL,
                {attribution:'&copy;2014 <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'});

                // need to get your own bing maps key, http://www.microsoft.com/maps/create-a-bing-maps-key.aspx
               //  var bingMapsLayer = new L.BingLayer("AnH1IKGCBwAiBWfYAHMtIfIhMVybHFx2GxsReNP5W0z6P8kRa67_QwhM4PglI9yL");
                var googleMapsLayer = new L.Google('ROADMAP');
            
                // this fixes the zoom buttons from freezing
                // https://github.com/shramov/leaflet-plugins/issues/62
                L.polyline([[0, 0], ]).addTo(gpsTrackerMap);

                // this sets which map layer will first be displayed
                gpsTrackerMap.addLayer(googleMapsLayer);

                // this is the switcher control to switch between map types

                //'Bing Maps':bingMapsLayer,
                gpsTrackerMap.addControl(new L.Control.Layers({
                    'OpenStreetMaps':openStreetMapsLayer,
                    'Google Maps':googleMapsLayer
                }, {}));
            }

                var finalLocation = false;
                var counter = 0;
                var locationArray = [];
                
                // iterate through the locations and create map markers for each location
                $(json.locations).each(function(key, value){
                    var latitude =  $(this).attr('latitude');
                    var longitude = $(this).attr('longitude');
                    var tempLocation = new L.LatLng(latitude, longitude);
                    
                    locationArray.push(tempLocation);                    
                    counter++;

                    // want to set the map center on the last location
                    if (counter == $(json.locations).length) {
                        //gpsTrackerMap.setView(tempLocation, zoom);  if using fixed zoom
                        finalLocation = true;
                    
                        if (!viewingAllRoutes) {
                            displayCityName(latitude, longitude);
                        }
                    }

                    var marker = createMarker(
                        latitude,
                        longitude,
                        $(this).attr('speed'),
                        $(this).attr('direction'),
                        $(this).attr('distance'),
                        $(this).attr('locationMethod'),
                        $(this).attr('gpsTime'),
                        $(this).attr('userName'),
                        $(this).attr('sessionID'),
                        $(this).attr('accuracy'),
                        $(this).attr('extraInfo'),
                        gpsTrackerMap, finalLocation);
                });
                
                // fit markers within window
                var bounds = new L.LatLngBounds(locationArray);
                gpsTrackerMap.fitBounds(bounds);
                 
            }
    }

    function createMarker(latitude, longitude, speed, direction, distance, locationMethod, gpsTime,
                          userName, sessionID, accuracy, extraInfo, map, finalLocation) {
        var iconUrl;

        if (finalLocation) {
            iconUrl = imgurl +'coolred_small.png';
        } else {
            iconUrl = imgurl +'coolgreen2_small.png';
        }

        var markerIcon = new L.Icon({
                iconUrl:      iconUrl,
                shadowUrl:    imgurl +'coolshadow_small.png',
                iconSize:     [30, 30],
                shadowSize:   [22, 20],
                iconAnchor:   [6, 20],
                shadowAnchor: [6, 20],
                popupAnchor:  [-3, -25]
        });

        var lastMarker = "</td></tr>";

        // when a user clicks on last marker, let them know it's final one
        if (finalLocation) {
            lastMarker = "</td></tr><tr><td align=left>&nbsp;</td><td><b>Final location</b></td></tr>";
        }

        // convert from meters to feet
        accuracy = parseInt(accuracy * 3.28);

        var popupWindowText = "<table border=0 style=\"font-size:95%;font-family:arial,helvetica,sans-serif;color:#000;\">" +
            "<tr><td align=right>&nbsp;</td><td>&nbsp;</td><td rowspan=2 align=right>" +
            "<img src=http://multisoftindonesia.com/gpstracker/assets/images/" + getCompassImage(direction) + ".jpg alt= />" + lastMarker +
            "<tr><td align=right>Speed:&nbsp;</td><td>" + speed +  " mph</td></tr>" +
            "<tr><td align=right>Distance:&nbsp;</td><td>" + distance +  " mi</td><td>&nbsp;</td></tr>" +
            "<tr><td align=right>Time:&nbsp;</td><td colspan=2>" + gpsTime +  "</td></tr>" +
            "<tr><td align=right>Name:&nbsp;</td><td>" + userName + "</td><td>&nbsp;</td></tr>" +
            "<tr><td align=right>Accuracy:&nbsp;</td><td>" + accuracy + " ft</td><td>&nbsp;</td></tr></table>";


        var gpstrackerMarker;
        var title = userName + " - " + gpsTime;

        // make sure the final red marker always displays on top 
        if (finalLocation) {
            gpstrackerMarker = new L.marker(new L.LatLng(latitude, longitude), {title: title, icon: markerIcon, zIndexOffset: 999}).bindPopup(popupWindowText).addTo(map);
        } else {
            gpstrackerMarker = new L.marker(new L.LatLng(latitude, longitude), {title: title, icon: markerIcon}).bindPopup(popupWindowText).addTo(map);
        }
        
        // if we are viewing all routes, we want to go to a route when a user taps on a marker instead of displaying popupWindow
        if (viewingAllRoutes) {
            gpstrackerMarker.unbindPopup();
            
            gpstrackerMarker.on("click", function() {         
                var url = baseurl +'tracker/getrouteformap/' + sessionID;

                viewingAllRoutes = false;
 
                var indexOfRouteInRouteSelectDropdwon = sessionIDArray.indexOf(sessionID) + 1;
                routeSelect.selectedIndex = indexOfRouteInRouteSelectDropdwon;

                if (autoRefresh) {
                    restartInterval(); 
                }

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        loadGPSLocations(data);
                    },
                    error: function (xhr, status, errorThrown) {
                        console.log("status: " + xhr.status);
                        console.log("errorThrown: " + errorThrown);
                    }
                 });
            }); // on click
        } 
    }

    function getCompassImage(azimuth) {
        if ((azimuth >= 337 && azimuth <= 360) || (azimuth >= 0 && azimuth < 23))
                return "compassN";
        if (azimuth >= 23 && azimuth < 68)
                return "compassNE";
        if (azimuth >= 68 && azimuth < 113)
                return "compassE";
        if (azimuth >= 113 && azimuth < 158)
                return "compassSE";
        if (azimuth >= 158 && azimuth < 203)
                return "compassS";
        if (azimuth >= 203 && azimuth < 248)
                return "compassSW";
        if (azimuth >= 248 && azimuth < 293)
                return "compassW";
        if (azimuth >= 293 && azimuth < 337)
                return "compassNW";

        return "";
    }
    
    // check to see if we have a map loaded, don't want to autorefresh or delete without it
    function hasMap() {
        if (routeSelect.selectedIndex == 0) { // means no map
            return false;
        }
        else {
            return true;
        }
    }

    function displayCityName(latitude, longitude) {
        var lat = parseFloat(latitude);
        var lng = parseFloat(longitude);
        var latlng = new google.maps.LatLng(lat, lng);
        var reverseGeocoder = new google.maps.Geocoder();
        reverseGeocoder.geocode({'latLng': latlng}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                // results[0] is full address
                if (results[1]) {
                    var reverseGeocoderResult = results[1].formatted_address; 
                    showPermanentMessage(reverseGeocoderResult);
                }
            } else {
                console.log('Geocoder failed due to: ' + status);
            }
        });
    }
 

    // message visible for 7 seconds
    function showMessage(message) {
        // if we show a message like start auto refresh, we want to put back our current address afterwards
        var tempMessage =  $('#messages').html();
        
        $('#messages').html(message);
        setTimeout(function() {
            $('#messages').html(tempMessage);
        }, 7 * 1000); // 7 seconds
    }

    function showPermanentMessage(message) {
        $('#messages').html(message);
    }

    // for debugging, console.log(objectToString(map));
    function objectToString (obj) {
        var str = '';
        for (var p in obj) {
            if (obj.hasOwnProperty(p)) {
                str += p + ': ' + obj[p] + '\n';
            }
        }
        return str;
    }
    
    function setTheme() {
        //var bodyBackgroundColor = $('body').css('backgroundColor');
        //$('.container').css('background-color', bodyBackgroundColor);
        //$('body').css('background-color', '#ccc');
        // $('head').append('<link rel="stylesheet" href="style2.css" type="text/css" />');        
    }
});

