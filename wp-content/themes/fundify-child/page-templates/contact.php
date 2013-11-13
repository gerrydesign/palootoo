<?php
/**
 * Template Name: Contact
 *
 * @package Fundify
 * @since Fundify 1.0
 */

get_header(); ?>

	<?php while ( have_posts() ) : the_post(); ?>
	<div id="title-image">
		<script>

var pal_loc = 'Victoria, MN';
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

<div id="image" style="min-width:100%;height:100%;"></div>
<!--///////////GERRYDESIGN MOD MAPPING////////////-->
		
		<h1><?php 
			$string = fundify_theme_mod( 'contact_text' ); 
			$lines = explode( "\n", $string );
		?>
		<span><?php echo implode( '</span><br /><span>', $lines ); ?></span></h1>
	</div>
	<div id="content">
		<div class="container">
			<div class="contacts">
				<div class="address">
					<div class="left contact-address"><?php echo wpautop( fundify_theme_mod( 'contact_address' ) ); ?></div>
					<div class="left"><a href="mailto:<?php echo get_option( 'admin_email' ); ?>"><?php echo get_option( 'admin_email' ); ?></a></div>
				</div>
				<h2 class="contact-subtitle"><?php echo fundify_theme_mod( 'contact_subtitle' ); ?></h2>
				<div class="div-c"></div>
				<div id="respond">
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</div>
				<!-- #respond --> 
			</div>
		</div>
		<!-- / container -->
	</div>
	<!-- / content -->
	<?php endwhile; ?>

<?php get_footer(); ?>