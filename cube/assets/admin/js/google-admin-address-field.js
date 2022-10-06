if(jQuery('.cwp-google-address').length > 0){
    function initialize_google_address() {
        
        jQuery('.cwp-google-address').each(function(){
            var thisObj = jQuery(this);
        
        
            var input_id  = thisObj.find('.address').attr('id');
            var latitude  = thisObj.find('.latitude').val();
            var longitude = thisObj.find('.longitude').val();

            
            var loadmap = loadMap(input_id,latitude,longitude);
            var marker  = loadmap[1];
            var map     = loadmap[0];

            var autocomplete = new google.maps.places.Autocomplete(document.getElementById(input_id));
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var place = autocomplete.getPlace();
                
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);
                marker.setVisible(true);
                
                var latitude = place.geometry.location.lat();
                var longitude = place.geometry.location.lng();
                thisObj.find('.latitude').val(latitude);
                thisObj.find('.longitude').val(longitude);
            });
            
            google.maps.event.addListener(marker, 'dragend', function(evt){
                geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            thisObj.find('.address').val(results[0].formatted_address);
                            thisObj.find('.latitude').val(marker.getPosition().lat());
                            thisObj.find('.longitude').val(marker.getPosition().lng());

                        }
                    }
                });
            });
            
        });
    }
    jQuery( window ).on( 'load', initialize_google_address );
    jQuery( '.cwp-add-row-btn' ).on( 'click', function() {
        setTimeout(function() {initialize_google_address();}, 1000);
    });
    jQuery(document).on( "click", ".cwp-get-current-location", function() {
        var thismap = jQuery(this);
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showCurrentPosition);
        }else{
            alert('Geolocation is not supported by your browser');
        }
        function showCurrentPosition(position){
            
            var input_id  = thismap.parents().children('.address').attr('id');
            var latitude  = thismap.parents().children('.latitude').val();
            var longitude = thismap.parents().children('.longitude').val();
            var loadmap = loadMap(input_id,latitude,longitude);
            var marker  = loadmap[1];
            var map     = loadmap[0];
            
            var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            thismap.parents().children('.latitude').val(position.coords.latitude);
            thismap.parents().children('.longitude').val(position.coords.longitude);
            var pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            };
            map.setCenter(pos);
            marker.setPosition(pos);
            marker.setVisible(true);

            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({ "latLng": latlng }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    console.log(results);
                    if (results[1]) {
                        thismap.parents().children('.address').val(results[1].formatted_address);
                    }
                }
            });
        }
    });
    
    function loadMap(input_id,latitude,longitude){

        if( typeof latitude == "undefined" || latitude == '' ){
            latitude = 51.5072;
        }
        if( typeof longitude == "undefined" || longitude == '' ){
            longitude = -0.128;
        }

        var latLng   = new google.maps.LatLng(latitude, longitude);
        var map = new google.maps.Map(document.getElementById("map-"+ input_id), {
            center: latLng,
            zoom: 14,
            minZoom: 0,
            maxZoom: 30,
            draggable: true,
            scrollwheel: false,
            navigationControl: !0,
            mapTypeControl: !1,
            streetViewControl: !1,
        });

        var marker = new google.maps.Marker({
            position: latLng,
            map: map,
            draggable: true
        });
        var maparray = new Array(map,marker);
         return maparray;
    }
}