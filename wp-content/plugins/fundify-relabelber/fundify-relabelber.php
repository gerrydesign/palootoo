<?php
/*
Plugin Name: Fundify to Palootoo
Plugin URI: 
Description: 
Version: 
Author: 
Author URI: 
License: 
License URI: 
*/

function nysc_project_labels( $labels ) {
    $labels = array(
	'name' 				=> __( 'Palootoo', 'atcf' ),
	'singular_name' 	=> __( 'Palootoo', 'atcf' ),
	'add_new' 			=> __( 'Add New', 'atcf' ),
	'add_new_item' 		=> __( 'Add New Palootoo', 'atcf' ),
	'edit_item' 		=> __( 'Edit Palootoo', 'atcf' ),
	'new_item' 			=> __( 'New Palootoo', 'atcf' ),
	'all_items' 		=> __( 'All Palootoo', 'atcf' ),
	'view_item' 		=> __( 'View Palootoo', 'atcf' ),
	'search_items' 		=> __( 'Search Palootoo', 'atcf' ),
	'not_found' 		=> __( 'No Palootoo found', 'atcf' ),
	'not_found_in_trash'=> __( 'No Palootoo found in Trash', 'atcf' ),
	'parent_item_colon' => '',
	'menu_name' 		=> __( 'Palootoo', 'atcf' )    
    );
 				
    return $labels;
}
add_filter( 'atcf_campaign_labels','nysc_project_labels' );
