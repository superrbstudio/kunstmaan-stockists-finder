function stockistsMap() {
    // Asynchronously Load the map API
    var script = document.createElement('script');
    script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=mapInitialize";
    document.body.appendChild(script);
}

function mapInitialize() {
    var jsonData = $('#stockists').data('stockists');
    var map;
    var bounds = new google.maps.LatLngBounds();
    var mapOptions = {
        mapTypeId: 'roadmap'
    };

    // Display a map on the page
    map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

    map.setTilt(45);

    // Display multiple markers on a map
    var infoWindow = new google.maps.InfoWindow(), marker, i;

    // Loop through our array of markers & place each one on the map
    for( i = 0; i < jsonData.length; i++ ) {
        var position = new google.maps.LatLng(jsonData[i]['latitude'], jsonData[i]['longitude']);
        bounds.extend(position);
        marker = new google.maps.Marker({
            position: position,
            map: map,
            title: jsonData[i]['stockist'],
            icon: "/frontend/img/map-pin.png"
        });

        // Allow each marker to have an info window
        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infoWindow.setContent('<h3>' + jsonData[i]['stockist'] + '</h3><p>' + jsonData[i]['address'] + ' ' + jsonData[i]['post_code'] + '</p><p>' + jsonData[i]['county'] + '</p><p><a href="http://' + jsonData[i]['website'] + '" target="_blank"' + jsonData[i]['website'] + '</p><a href="http://maps.google.com/?q=' + jsonData[i]['latitude'] + ',' + jsonData[i]['longitude'] + '" target="_blank">View on map</a>');
                infoWindow.open(map, marker);
            }
        })(marker, i));

        // Automatically center the map fitting all markers on the screen
        map.fitBounds(bounds);
    }

    var styles = [
        {
            "stylers": [
                { "saturation": -100 }
            ]
        }
    ]

    map.setOptions({styles: styles});

    // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
        google.maps.event.removeListener(boundsListener);
    });

}