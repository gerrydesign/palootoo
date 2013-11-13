<?php
/**
 *
 */

global $post, $campaign;

$author = get_user_by( 'id', $post->post_author );
?>

<div class="widget widget-bio">
	<h3><?php _e( 'Approximate Delivery Area', 'fundify' ); ?></h3>

					<script>

var pal_loc = "<?php echo $campaign->location(); ?>";
var geocoder = new google.maps.Geocoder();
var map;
var cityCircle;
var coord;
var marker;

var map_style = [
  {
  },{
    "featureType": "road",
    "elementType": "geometry.fill",
    "stylers": [
      { "color": "#12a797" }
    ]
  },{
    "featureType": "landscape",
    "stylers": [
      { "color": "#b2b1b1" }
    ]
  },{
    "featureType": "poi",
    "elementType": "geometry.fill",
    "stylers": [
      { "color": "#648580" }
    ]
  },{
    "featureType": "road",
    "elementType": "labels.text.stroke",
    "stylers": [
      { "color": "#ffffff" }
    ]
  },{
    "featureType": "poi.park",
    "stylers": [
      { "color": "#415252" }
    ]
  }
];


function initialize() {
  var latlng = codeAddress();
  var mapOptions = {
    center: latlng,
    zoom: 16,
    zoomControl: false,
    draggable:false,
    disableDefaultUI:true,
    scrollwheel: false,
    mapTypeId:google.maps.MapTypeId.ROADMAP
  }
  map = new google.maps.Map(document.getElementById('image'), mapOptions);
  map.setOptions({styles: map_style});
}

function codeAddress() {
  //var address = document.getElementById('address').value;
  geocoder.geocode( { 'address': pal_loc}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      map.setCenter(results[0].geometry.location);
      marker = new google.maps.Marker({
          map: map,
          position: results[0].geometry.location


      });

    //   var populationOptions = {
    //   strokeColor: '#FF0000',
    //   strokeOpacity: 0.3,
    //   strokeWeight: 1,
    //   fillColor: '#FF0000',
    //   fillOpacity: 0.1,
    //   map: map,
    //   center:results[0].geometry.location,
  	 //  radius:1000
    // };
    // cityCircle = new google.maps.Circle(populationOptions); 
    } else {
      alert('Geocode was not successful for the following reason: ' + status);
    }
  });


}

google.maps.event.addDomListener(window, 'load', initialize);
</script>

<div id="image" style="min-width:320px;height:320px;"></div>
<!--///////////GERRYDESIGN MOD MAPPING////////////-->

	
</div>
