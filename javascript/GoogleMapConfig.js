$(function () {
    // initialise
    initialize();
});

var markers = [];

function initialize() {

    // set viewport for device (see below)
    detectBrowser();

    // default to New Zealand TODO: make this a part of the admin
    var mapInitialView = {
        center: {
            lat: -41.07935114946898,
            lng: 172.46337890625
        },
        zoom: 5,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    // get the map element
    var polyLine;
    var polyOptions;
    var iconSize = 1.5;

    var map = new google.maps.Map(document.getElementById("map_canvas"), mapInitialView);

    var icon = {

        path: "M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0",
        fillColor: '#3785ff',
        fillOpacity: .6,
        anchor: new google.maps.Point(0,0),
        strokeWeight: 0,
        scale: iconSize
    }

    // map.panTo(new google.maps.LatLng('-35.281810', '174.091573'));
    // map.panTo(new google.maps.LatLng('-35.167780', '173.152070'));

    // construct infowindow
    var infowindow = new google.maps.InfoWindow({
        content: ''
    });
    // the ajax object, populated with address and infoWindow
    $.ajax({
        url: window.location.href +'/new-location-map-page-2/locationData',
        type: 'GET',
        success: function (result) {
            var locations = JSON.parse(result);
            for (var i = 0, length = locations.length; i < length; i++) {

                var locationData = locations[i];
                new google.maps.Marker({
                    position: new google.maps.LatLng(locationData.lat, locationData.lng),
                    map: map,
                    draggable: false,
                    icon: icon,
                    zIndex : -20
                });
                // map.panTo(new google.maps.LatLng(locationData.lat, locationData.lng));

                // var latLng = new google.maps.LatLng(locationData.lat, locationData.lng);
                // drop the marker on the map
                // addMarkerWithTimeout(latLng, map, infowindow, locationData, i * 100);
            }
        }
    });
}

function detectBrowser() {
    // Make everything look cool across all devices
    var useragent = navigator.userAgent;
    var mapdiv = document.getElementById("map_canvas");

    if (useragent.indexOf('iPhone') != -1 || useragent.indexOf('Android') != -1) {
        mapdiv.style.width = '100%';
        mapdiv.style.height = '100%';
    } else {
        mapdiv.style.width = '100%';
        mapdiv.style.height = '500px';
    }
}

function addMarkerWithTimeout(position, map, infowindow, location, timeout) {
    // google restricts the amount of requests per second, dropping with a timeout gets past this
    window.setTimeout(function () {
        var marker = new google.maps.Marker({
            position: position,
            map: map,
            animation: google.maps.Animation.DROP
        });
        markers.push(marker);
        bindInfoWindow(marker, map, infowindow, location.info);
    }, timeout);
}

function bindInfoWindow(marker, map, infowindow, description) {
    // bind the infowindow to the marker
    google.maps.event.addListener(marker, 'click', function () {
        infowindow.setContent(description);
        infowindow.open(map, marker);
    });
}
