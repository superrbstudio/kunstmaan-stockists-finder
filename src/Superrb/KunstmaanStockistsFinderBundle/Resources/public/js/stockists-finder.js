function stockistsMap() {
    if($('html').hasClass('maps-api-loaded')) {
        //init the map as api already loaded
        mapInitialize();
    } else {
        // Asynchronously Load the map API (first time load)
        var script = document.createElement('script');
        script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=mapInitialize";
        document.body.appendChild(script);
    }
}

function mapInitialize() {
    $('body').removeClass("loading");

    //add a class to the body to indicate that google maps has loaded
    $('html').addClass('maps-api-loaded');

    var container = $('#stockists-wrapper');

    $('.country select').on('change', function() {
        //clear the postcode
        $(this).closest('form').find('input#stockists_finder_form_postcode').val('');
        //submit the form when a selection is made
        if($("#stockists_finder_form_country").val() != 'GB') {
            $('.search-wrapper').removeClass('active');
            $('body').addClass("loading");
            $(this).closest('form').submit();
        } else {
            $('.search-wrapper').addClass('active');
        }
    });

    //catch the submission of the form
    $('form.stockists-finder').submit(function(e){
        //lets disable the button also, incase they try to click it super quick
        $(this).find('button').attr('disabled', 'disabled').text("Searching...");
        //create and submit the form through AJAX
        $.ajax({
            url: $(this).attr('action'),
            type : $(this).attr('method'),
            data : $(this).serialize(),
            success : function(data) {
                // replace the content of the container with the new data
                container.html(data);
                //re-init this function
                stockistsMap();
            }
        });

        e.preventDefault();
    });

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
                var postcode = '';
                if(jsonData[i]['postcode'] != null) {
                    postcode = ' ' + jsonData[i]['postcode'];
                }
                var county = '';
                if(jsonData[i]['county'] != null) {
                    county = '<p>' + jsonData[i]['county'] + '</p>';
                }
                var website = '';
                if(jsonData[i]['website'] != null) {
                    website = '<p><a href="http://' + jsonData[i]['website'] + '" target="_blank">' + jsonData[i]['website'] + '</a></p>';
                }
                infoWindow.setContent('<div class="info-marker"><p class="title">' + jsonData[i]['stockist'] + '</p><p>' + jsonData[i]['address'] + postcode + '</p>' + county + website + '</div>');
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