<?php

/*
  Plugin Name: MetaTag SEO for WordPress
  Plugin URI: http://en.ibabar.com
  Description: An onpage SEO plugin for WordPress
  Version: 1.0
  Author: Babar
  Author URI: http://www.ibabar.com
*/



add_action( 'add_meta_boxes', 'metatag_data_box' );
function metatag_data_box() {
    add_meta_box( 
        'metatag_data_box',
        __( 'MetaTag On Page SEO', 'metatag' ),
        'metatag_data_box_content',
        'post',
        'advanced',
        'default'
    );
    
    add_meta_box( 
        'metatag_data_box',
        __( 'MetaTag On Page SEO', 'metatag' ),
        'metatag_data_box_content',
        'page',
        'advanced',
        'default'
    );
}



function metatag_data_box_content( $post ) {
	wp_nonce_field( plugin_basename( __FILE__ ), 'metatag_data_box_content_nonce' );
	
	$metadescription = get_post_meta( $post->ID, '_metadescription', true );
	$metakeywords = get_post_meta( $post->ID, '_metakeywords', true );
	echo '';
	echo 'Meta Description (No more than 160 characters):     <textarea rows="1" cols="60" name="metadescription">'.$metadescription. '</textarea> <br>';
	echo '';
	echo 'Meta Keywords (Comma Separated): <input type="text" name="metakeywords" value="'.$metakeywords.'" size="60"><br>';
	echo '';
}

add_action( 'save_post', 'metatag_data_box_save' );
function metatag_data_box_save( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	return;

	if ( !wp_verify_nonce( $_POST['metatag_data_box_content_nonce'], plugin_basename( __FILE__ ) ) )
	return;

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	}
	
	//Code per field
	$metadescription = $_POST['metadescription'];
	$metadescription_value = get_post_meta( $post_id, '_metadescription', true );
	if ( $metadescription && '' == $metadescription_value )
		add_post_meta( $post_id, '_metadescription', $metadescription, true );
	elseif ( $metadescription && $metadescription != $metadescription_value )
		update_post_meta( $post_id, '_metadescription', $metadescription );
		
	$metakeywords = $_POST['metakeywords'];
	$metakeywords_value = get_post_meta( $post_id, '_metakeywords', true );
	if ( $metakeywords && '' == $metakeywords_value )
		add_post_meta( $post_id, '_metakeywords', $metakeywords, true );
	elseif ( $metakeywords && $metakeywords != $metakeywords_value )
		update_post_meta( $post_id, '_metakeywords', $metakeywords );
}


add_action ('wp_head', 'metatag');

function metatag( ) {
	global $post;
	$post_id = $post->ID;
	$metadescription = get_post_meta( $post->ID, '_metadescription', true );
	$metakeywords = get_post_meta( $post->ID, '_metakeywords', true );
	
	echo '<meta name="description" content="' . $metadescription . '" />
	';
	echo '<meta name="keywords" content="' . $metakeywords . '" />
	';
	
}