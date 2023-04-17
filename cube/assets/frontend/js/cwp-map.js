
function Tiles() {
    var tiles = false;
    if (cubewp_map_params.map_option == 'google') {
        tiles = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 18,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            noWrap: true,
            attribution: '&copy; Map data ©2022 <a href="https://www.google.com">Google</a>'
        });
    } else if (cubewp_map_params.map_option == 'openstreet') {
        tiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18, attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        });
    } else if (cubewp_map_params.map_option == 'mapbox') {
        tiles = L.tileLayer('https://api.mapbox.com/styles/v1/' + cubewp_map_params.map_style + '/tiles/256/{z}/{x}/{y}?access_token=' + cubewp_map_params.mapbox_token, {
            maxZoom: 18,
            attribution: 'Map data ©<a href="http://openstreetmap.org">OpenStreetMap</a>' + 'Imagery © <a href="http://mapbox.com">Mapbox</a>',
        });
    }

    return tiles;
}

function cwp_rand_id(length, preFix = '', postFix = '') {
    var char = 'abcdefghijklmnopqrstuvwxyz',
        ID = '';
    if (typeof length !== "number") length = 6;
    for (var i = length; i > 0; i--) {
        ID += char[Math.floor(Math.random() * char.length)];
    }
    return preFix + ID + postFix;
}

function CWP_Single_Map() {
    var cptSingleMap = jQuery('.cpt-single-map');
    if (cptSingleMap.length > 0) {
        cptSingleMap.each(function () {
            var thisObj = jQuery(this),
                latitude = parseFloat(thisObj.attr('data-latitude')),
                longitude = parseFloat(thisObj.attr('data-longitude')),
                uniqueID = cwp_rand_id(6, 'cwp-map-'),
                tiles = Tiles();
            thisObj.empty();
            thisObj.html('<div id="' + uniqueID + '"></div>');

            if (typeof latitude == "undefined" || latitude === '') latitude = cubewp_map_params.map_latitude;
            if (typeof longitude == "undefined" || longitude === '') longitude = cubewp_map_params.map_longitude;

            if (typeof latitude == "undefined" || latitude === '') latitude = 51.5072;
            if (typeof longitude == "undefined" || longitude === '') longitude = -0.128;

            if (checkIfValidlatitudeAndlongitude(latitude + ',' + latitude)) {
                var map = latlng = marker = null;
                latlng = new L.latLng(latitude, longitude);
                jQuery('#' + uniqueID).css('height', '100%');
                map = new L.map(uniqueID, {center: latlng, zoom: cubewp_map_params.map_zoom, layers: [tiles]});
                marker = new L.marker(new L.LatLng(latitude, longitude));
                map.addLayer(marker);
            }
        });
    }
}

CWP_Single_Map();

function checkIfValidlatitudeAndlongitude(str) {
    // Regular expression to check if string is a latitude and longitude
    const regexExp = /^((\-?|\+?)?\d+(\.\d+)?),\s*((\-?|\+?)?\d+(\.\d+)?)$/gi;
  
    return regexExp.test(str);
  }
function CWP_Cluster_Map(args ='') {
    var cwpArchiveMap = jQuery('.cwp-archive-content-map');
    if(cwpArchiveMap.length > 0){
        var tiles = Tiles(),
            latlng = L.latLng(cubewp_map_params.map_latitude, cubewp_map_params.map_longitude);
        if (!tiles) {
            return false;
        }
        var MapID = 'archive-map';
        cwpArchiveMap.empty();
        cwpArchiveMap.html('<div id="' + MapID + '"></div>');
        var map = L.map(MapID, {center: latlng,fullscreenControl: false, zoom: cubewp_map_params.map_zoom, layers: [tiles]});
        map.addControl(new L.Control.Fullscreen());
        if(args !== ''){
            var markers = L.markerClusterGroup();
            var showmap = 'false';
            for (var i = 0; i < args.length; i++) {
                if(checkIfValidlatitudeAndlongitude(args[i][0]+','+args[i][1])){
                    showmap = 'true';
                    var a = args[i],
                        title = a[2],
                        url = a[3],
                        thumbnail = a[4],
                        popover = '',
                        marker = L.marker(new L.LatLng(a[0], a[1]), {title: title});
                    popover = '<div class="cwp-map-popover">' +
                        '<a href="' + url + '" target="_blank"><img src="' + thumbnail + '" alt="' + title + '" />' +
                        '<h3>' + title + '</h3></a>' +
                    '</div>';
                    marker.bindPopup(popover);
                    markers.addLayer(marker);
                }
            }
            if(showmap == 'true'){
                map.addLayer(markers);
                map.fitBounds(markers.getBounds(), {padding: [50, 50]});
                map.scrollWheelZoom.enable();
                map.invalidateSize();
                map.dragging.enable();
            }
        }
    }
}
